<?php

declare(strict_types=1);

namespace App\Domains\Ai\Services;

use App\Domains\Ai\DTOs\AiKnowledgeSourceData;
use App\Domains\Ai\Enums\AiKnowledgeSourceStatus;
use App\Domains\Ai\Events\KnowledgeIndexed;
use App\Domains\Ai\Rules\AiKnowledgeSourceRules;
use App\Models\AiKnowledgeSource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class KnowledgeService
{
    public function __construct(
        protected AiKnowledgeSource $model = new AiKnowledgeSource(),
    ) {
    }

    public function create(array $data): AiKnowledgeSourceData
    {
        $validated = AiKnowledgeSourceRules::validate($data, 'create');
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['status'] = $validated['status'] ?? AiKnowledgeSourceStatus::Pending->value;
        $this->validateUniqueSlugPerSchool($validated['school_id'], $validated['name'], null);

        return DB::transaction(function () use ($validated) {
            $source = $this->model->create($validated);

            return AiKnowledgeSourceData::create($source->toArray());
        });
    }

    public function update(AiKnowledgeSource $source, array $data): AiKnowledgeSourceData
    {
        $validated = AiKnowledgeSourceRules::validate($data, 'update', $source->id);

        if (isset($validated['name'])) {
            $this->validateUniqueSlugPerSchool($source->school_id, $validated['name'], $source->id);
        }

        $source->update($validated);

        return AiKnowledgeSourceData::create($source->fresh()->toArray());
    }

    public function delete(AiKnowledgeSource $source): void
    {
        $source->delete();
    }

    public function startIndexing(AiKnowledgeSource $source): AiKnowledgeSourceData
    {
        $source->update(['status' => AiKnowledgeSourceStatus::Indexing->value]);

        return AiKnowledgeSourceData::create($source->fresh()->toArray());
    }

    public function completeIndexing(AiKnowledgeSource $source, int $chunkCount, int $totalTokens): AiKnowledgeSourceData
    {
        $source->update([
            'status' => AiKnowledgeSourceStatus::Indexed->value,
            'chunk_count' => $chunkCount,
            'total_tokens' => $totalTokens,
            'indexed_at' => now(),
            'version' => $source->version + 1,
        ]);

        KnowledgeIndexed::dispatch($source->fresh());

        return AiKnowledgeSourceData::create($source->fresh()->toArray());
    }

    public function markFailed(AiKnowledgeSource $source, ?string $error = null): AiKnowledgeSourceData
    {
        $source->update([
            'status' => AiKnowledgeSourceStatus::Failed->value,
            'metadata' => array_merge($source->metadata ?? [], ['last_error' => $error]),
        ]);

        return AiKnowledgeSourceData::create($source->fresh()->toArray());
    }

    public function markOutdated(AiKnowledgeSource $source): AiKnowledgeSourceData
    {
        $source->update(['status' => AiKnowledgeSourceStatus::Outdated->value]);

        return AiKnowledgeSourceData::create($source->fresh()->toArray());
    }

    public function reindex(AiKnowledgeSource $source): AiKnowledgeSourceData
    {
        return $this->startIndexing($source);
    }

    public function findById(string $id): AiKnowledgeSource
    {
        return $this->model->findOrFail($id);
    }

    public function getActiveForSchool(string $schoolId): \Illuminate\Support\Collection
    {
        return $this->model
            ->where('school_id', $schoolId)
            ->where('status', AiKnowledgeSourceStatus::Indexed->value)
            ->get();
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

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
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
            throw ValidationException::withMessages(['name' => 'A knowledge source with this name already exists in this school.']);
        }
    }
}
