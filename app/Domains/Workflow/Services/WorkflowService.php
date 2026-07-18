<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Services;

use App\Domains\Workflow\DTOs\WorkflowData;
use App\Domains\Workflow\DTOs\WorkflowVersionData;
use App\Domains\Workflow\Enums\WorkflowStatus;
use App\Domains\Workflow\Enums\WorkflowVersionStatus;
use App\Domains\Workflow\Events\WorkflowCreated;
use App\Domains\Workflow\Events\WorkflowPublished;
use App\Domains\Workflow\Rules\WorkflowRules;
use App\Domains\Workflow\Rules\WorkflowVersionRules;
use App\Models\Workflow;
use App\Models\WorkflowVersion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WorkflowService
{
    public function __construct(
        protected Workflow $model = new Workflow(),
        protected WorkflowVersion $versionModel = new WorkflowVersion(),
    ) {
    }

    public function create(array $data): WorkflowData
    {
        $validated = WorkflowRules::validate($data, 'create');
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $this->validateUniqueSlugPerSchool($validated['school_id'], $validated['name'], null);

        return DB::transaction(function () use ($validated) {
            $workflow = $this->model->create($validated);

            $this->versionModel->create([
                'school_id' => $workflow->school_id,
                'workflow_id' => $workflow->id,
                'version' => 1,
                'status' => WorkflowVersionStatus::Draft->value,
                'nodes' => [['id' => 'start', 'type' => 'start', 'name' => 'Start']],
                'edges' => [],
            ]);

            WorkflowCreated::dispatch($workflow);

            return WorkflowData::create($workflow->toArray());
        });
    }

    public function update(Workflow $workflow, array $data): WorkflowData
    {
        $validated = WorkflowRules::validate($data, 'update', $workflow->id);

        if (isset($validated['name'])) {
            $this->validateUniqueSlugPerSchool($workflow->school_id, $validated['name'], $workflow->id);
            $validated['slug'] = Str::slug($validated['name']);
        }

        $workflow->update($validated);

        return WorkflowData::create($workflow->fresh()->toArray());
    }

    public function publish(Workflow $workflow, string $publishedBy): WorkflowData
    {
        $draftVersion = $this->versionModel
            ->where('workflow_id', $workflow->id)
            ->where('status', WorkflowVersionStatus::Draft->value)
            ->first();

        if (! $draftVersion) {
            throw ValidationException::withMessages([
                'version' => 'No draft version found to publish.',
            ]);
        }

        $this->validateWorkflowGraph($draftVersion);

        return DB::transaction(function () use ($workflow, $draftVersion, $publishedBy) {
            $this->versionModel->where('workflow_id', $workflow->id)
                ->where('status', WorkflowVersionStatus::Published->value)
                ->update(['status' => WorkflowVersionStatus::Archived->value]);

            $draftVersion->update([
                'status' => WorkflowVersionStatus::Published->value,
                'published_by' => $publishedBy,
                'published_at' => now(),
            ]);

            $workflow->update([
                'status' => WorkflowStatus::Active->value,
                'current_version' => $draftVersion->version,
            ]);

            WorkflowPublished::dispatch($workflow, $draftVersion->fresh());

            return WorkflowData::create($workflow->fresh()->toArray());
        });
    }

    public function updateVersion(WorkflowVersion $version, array $data): WorkflowVersionData
    {
        if ($version->status === WorkflowVersionStatus::Published->value) {
            throw ValidationException::withMessages([
                'version' => 'Published versions are immutable.',
            ]);
        }

        $validated = WorkflowVersionRules::validate($data);

        if (isset($validated['nodes'])) {
            $version->update(['nodes' => $validated['nodes']]);
        }

        if (isset($validated['edges'])) {
            $version->update(['edges' => $validated['edges']]);
        }

        if (isset($validated['changelog'])) {
            $version->update(['changelog' => $validated['changelog']]);
        }

        if (isset($validated['settings'])) {
            $version->update(['settings' => $validated['settings']]);
        }

        return WorkflowVersionData::create($version->fresh()->toArray());
    }

    public function archive(Workflow $workflow): WorkflowData
    {
        $workflow->update(['status' => WorkflowStatus::Archived->value, 'is_active' => false]);

        return WorkflowData::create($workflow->fresh()->toArray());
    }

    public function delete(Workflow $workflow): void
    {
        $hasExecutions = $this->model->find($workflow->id)->executions()->exists();

        if ($hasExecutions) {
            $workflow->update(['is_active' => false, 'status' => WorkflowStatus::Archived->value]);

            return;
        }

        $workflow->delete();
    }

    public function findById(string $id): Workflow
    {
        return $this->model->with(['activeVersion', 'triggers', 'schedules'])->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    private function validateUniqueSlugPerSchool(string $schoolId, string $name, ?string $excludeId): void
    {
        $slug = Str::slug($name);
        $query = $this->model->where('school_id', $schoolId)->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A workflow with this name already exists in this school.',
            ]);
        }
    }

    private function validateWorkflowGraph(WorkflowVersion $version): void
    {
        $nodes = $version->nodes ?? [];
        $edges = $version->edges ?? [];

        $nodeIds = array_column($nodes, 'id');
        $nodeTypes = array_column($nodes, 'type');

        if (! in_array('start', $nodeTypes)) {
            throw ValidationException::withMessages([
                'graph' => 'Workflow must have a start node.',
            ]);
        }

        if (! in_array('end', $nodeTypes)) {
            throw ValidationException::withMessages([
                'graph' => 'Workflow must have an end node.',
            ]);
        }

        $this->detectCircularDependencies($nodeIds, $edges);
    }

    private function detectCircularDependencies(array $nodeIds, array $edges): void
    {
        $adjacencyList = [];
        foreach ($nodeIds as $nodeId) {
            $adjacencyList[$nodeId] = [];
        }

        foreach ($edges as $edge) {
            $source = $edge['source'] ?? null;
            $target = $edge['target'] ?? null;

            if ($source && $target && isset($adjacencyList[$source])) {
                $adjacencyList[$source][] = $target;
            }
        }

        $visited = [];
        $recStack = [];

        foreach ($nodeIds as $nodeId) {
            if ($this->detectCycle($nodeId, $adjacencyList, $visited, $recStack)) {
                throw ValidationException::withMessages([
                    'graph' => 'Workflow contains a circular dependency.',
                ]);
            }
        }
    }

    private function detectCycle(string $node, array $adjacencyList, array &$visited, array &$recStack): bool
    {
        if (! isset($visited[$node])) {
            $visited[$node] = true;
            $recStack[$node] = true;

            foreach ($adjacencyList[$node] ?? [] as $neighbor) {
                if (! isset($visited[$neighbor]) && $this->detectCycle($neighbor, $adjacencyList, $visited, $recStack)) {
                    return true;
                }

                if (isset($recStack[$neighbor])) {
                    return true;
                }
            }
        }

        unset($recStack[$node]);

        return false;
    }
}
