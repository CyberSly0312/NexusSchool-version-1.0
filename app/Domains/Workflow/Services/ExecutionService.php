<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Services;

use App\Domains\Workflow\DTOs\WorkflowExecutionData;
use App\Domains\Workflow\DTOs\WorkflowTaskData;
use App\Domains\Workflow\Enums\ExecutionStatus;
use App\Domains\Workflow\Enums\TaskStatus;
use App\Domains\Workflow\Enums\TaskType;
use App\Domains\Workflow\Events\TaskAssigned;
use App\Domains\Workflow\Events\TaskCompleted;
use App\Domains\Workflow\Events\WorkflowCompleted;
use App\Domains\Workflow\Events\WorkflowFailed;
use App\Domains\Workflow\Events\WorkflowStarted;
use App\Domains\Workflow\Rules\WorkflowExecutionRules;
use App\Models\Workflow;
use App\Models\WorkflowExecution;
use App\Models\WorkflowTask;
use App\Models\WorkflowVersion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExecutionService
{
    public function __construct(
        protected WorkflowExecution $model = new WorkflowExecution(),
        protected WorkflowTask $taskModel = new WorkflowTask(),
    ) {
    }

    public function startExecution(array $data): WorkflowExecutionData
    {
        $validated = WorkflowExecutionRules::validate($data);

        $workflow = Workflow::findOrFail($validated['workflow_id']);

        if (! $workflow->is_active) {
            throw ValidationException::withMessages([
                'workflow_id' => 'This workflow is not active.',
            ]);
        }

        $version = WorkflowVersion::where('workflow_id', $workflow->id)
            ->where('status', 'published')
            ->first();

        if (! $version) {
            throw ValidationException::withMessages([
                'workflow_id' => 'This workflow has no published version.',
            ]);
        }

        if (isset($validated['idempotency_key'])) {
            $existing = $this->model->where('idempotency_key', $validated['idempotency_key'])
                ->where('school_id', $validated['school_id'])
                ->first();

            if ($existing) {
                return WorkflowExecutionData::create($existing->toArray());
            }
        }

        return DB::transaction(function () use ($validated, $workflow, $version) {
            $execution = $this->model->create([
                ...$validated,
                'workflow_version_id' => $version->id,
                'status' => ExecutionStatus::Running->value,
                'started_at' => now(),
            ]);

            $startNode = collect($version->nodes ?? [])->firstWhere('type', 'start');

            if ($startNode) {
                $execution->update(['current_node_id' => $startNode['id']]);
            }

            WorkflowStarted::dispatch($workflow, $execution);

            $this->createTasksForExecution($execution, $version);

            return WorkflowExecutionData::create($execution->toArray());
        });
    }

    public function completeExecution(WorkflowExecution $execution): WorkflowExecutionData
    {
        return DB::transaction(function () use ($execution) {
            $execution->update([
                'status' => ExecutionStatus::Completed->value,
                'completed_at' => now(),
                'duration_ms' => $execution->started_at
                    ? (int) $execution->started_at->diffInMilliseconds(now())
                    : null,
            ]);

            WorkflowCompleted::dispatch($execution);

            return WorkflowExecutionData::create($execution->fresh()->toArray());
        });
    }

    public function failExecution(WorkflowExecution $execution, ?string $error = null): WorkflowExecutionData
    {
        return DB::transaction(function () use ($execution, $error) {
            $execution->update([
                'status' => ExecutionStatus::Failed->value,
                'error' => $error,
                'completed_at' => now(),
                'duration_ms' => $execution->started_at
                    ? (int) $execution->started_at->diffInMilliseconds(now())
                    : null,
            ]);

            WorkflowFailed::dispatch($execution, $error);

            return WorkflowExecutionData::create($execution->fresh()->toArray());
        });
    }

    public function cancelExecution(WorkflowExecution $execution): WorkflowExecutionData
    {
        if ($execution->status->isTerminal()) {
            throw ValidationException::withMessages([
                'status' => 'Cannot cancel a terminal execution.',
            ]);
        }

        $execution->update([
            'status' => ExecutionStatus::Cancelled->value,
            'completed_at' => now(),
        ]);

        $this->taskModel->where('workflow_execution_id', $execution->id)
            ->whereIn('status', [TaskStatus::Pending->value, TaskStatus::Assigned->value, TaskStatus::InProgress->value])
            ->update(['status' => TaskStatus::Cancelled->value]);

        return WorkflowExecutionData::create($execution->fresh()->toArray());
    }

    public function retryExecution(WorkflowExecution $execution): WorkflowExecutionData
    {
        if (! $execution->status->isRetryable()) {
            throw ValidationException::withMessages([
                'status' => 'Only failed or timed-out executions can be retried.',
            ]);
        }

        if ($execution->retry_count >= $execution->max_retries) {
            throw ValidationException::withMessages([
                'retry_count' => 'Maximum retry count reached.',
            ]);
        }

        return DB::transaction(function () use ($execution) {
            $execution->update([
                'status' => ExecutionStatus::Pending->value,
                'error' => null,
                'retry_count' => $execution->retry_count + 1,
                'started_at' => null,
                'completed_at' => null,
                'duration_ms' => null,
            ]);

            return WorkflowExecutionData::create($execution->fresh()->toArray());
        });
    }

    public function completeTask(WorkflowTask $task, ?array $outputData = null, ?string $notes = null): WorkflowTaskData
    {
        return DB::transaction(function () use ($task, $outputData, $notes) {
            $task->update([
                'status' => TaskStatus::Completed->value,
                'output_data' => $outputData,
                'notes' => $notes,
                'completed_at' => now(),
                'started_at' => $task->started_at ?? now(),
                'duration_ms' => $task->started_at
                    ? (int) $task->started_at->diffInMilliseconds(now())
                    : null,
            ]);

            TaskCompleted::dispatch($task);

            $this->checkExecutionProgress($task->execution);

            return WorkflowTaskData::create($task->fresh()->toArray());
        });
    }

    public function assignTask(WorkflowTask $task, string $assignedTo, ?string $assignedBy = null): WorkflowTaskData
    {
        $task->update([
            'status' => TaskStatus::Assigned->value,
            'assigned_to' => $assignedTo,
            'assigned_by' => $assignedBy,
            'assigned_at' => now(),
        ]);

        TaskAssigned::dispatch($task);

        return WorkflowTaskData::create($task->fresh()->toArray());
    }

    public function findById(string $id): WorkflowExecution
    {
        return $this->model->with(['workflow', 'version', 'tasks', 'trigger'])->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('workflow', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['workflow_id'])) {
            $query->where('workflow_id', $filters['workflow_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    private function createTasksForExecution(WorkflowExecution $execution, WorkflowVersion $version): void
    {
        $nodes = $version->nodes ?? [];

        foreach ($nodes as $index => $node) {
            if (in_array($node['type'], ['start', 'end'])) {
                continue;
            }

            if ($node['type'] === 'approval') {
                $this->taskModel->create([
                    'school_id' => $execution->school_id,
                    'workflow_execution_id' => $execution->id,
                    'node_id' => $node['id'],
                    'type' => TaskType::Approval->value,
                    'name' => $node['name'] ?? 'Approval Task',
                    'status' => TaskStatus::Pending->value,
                    'order' => $index,
                    'input_data' => $node['config'] ?? null,
                ]);
            } elseif ($node['type'] === 'notification') {
                $this->taskModel->create([
                    'school_id' => $execution->school_id,
                    'workflow_execution_id' => $execution->id,
                    'node_id' => $node['id'],
                    'type' => TaskType::Notification->value,
                    'name' => $node['name'] ?? 'Notification Task',
                    'status' => TaskStatus::Pending->value,
                    'order' => $index,
                    'input_data' => $node['config'] ?? null,
                ]);
            } elseif ($node['type'] === 'ai_action') {
                $this->taskModel->create([
                    'school_id' => $execution->school_id,
                    'workflow_execution_id' => $execution->id,
                    'node_id' => $node['id'],
                    'type' => TaskType::AiAction->value,
                    'name' => $node['name'] ?? 'AI Action Task',
                    'status' => TaskStatus::Pending->value,
                    'order' => $index,
                    'input_data' => $node['config'] ?? null,
                ]);
            } elseif ($node['type'] === 'database_action') {
                $this->taskModel->create([
                    'school_id' => $execution->school_id,
                    'workflow_execution_id' => $execution->id,
                    'node_id' => $node['id'],
                    'type' => TaskType::DatabaseAction->value,
                    'name' => $node['name'] ?? 'Database Action Task',
                    'status' => TaskStatus::Pending->value,
                    'order' => $index,
                    'input_data' => $node['config'] ?? null,
                ]);
            }
        }
    }

    private function checkExecutionProgress(WorkflowExecution $execution): void
    {
        $pendingTasks = $this->taskModel
            ->where('workflow_execution_id', $execution->id)
            ->whereIn('status', [TaskStatus::Pending->value, TaskStatus::Assigned->value, TaskStatus::InProgress->value])
            ->exists();

        if (! $pendingTasks) {
            $this->completeExecution($execution);
        }
    }
}
