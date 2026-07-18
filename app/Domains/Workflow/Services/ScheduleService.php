<?php

declare(strict_types=1);

namespace App\Domains\Workflow\Services;

use App\Domains\Workflow\Enums\TriggerType;
use App\Jobs\Workflow\ProcessScheduledWorkflow;
use App\Models\Workflow;
use App\Models\WorkflowSchedule;
use App\Models\WorkflowTrigger;
use Carbon\CronExpression;
use Illuminate\Validation\ValidationException;

class ScheduleService
{
    public function __construct(
        protected WorkflowSchedule $model = new WorkflowSchedule(),
    ) {
    }

    public function createSchedule(Workflow $workflow, array $data): WorkflowSchedule
    {
        $validated = \Validator::make($data, [
            'name' => ['required', 'string', 'max:200'],
            'cron_expression' => ['required', 'string', 'max:100'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'payload' => ['nullable', 'array'],
            'conditions' => ['nullable', 'array'],
        ])->validate();

        if (! CronExpression::isValid($validated['cron_expression'])) {
            throw ValidationException::withMessages([
                'cron_expression' => 'Invalid cron expression.',
            ]);
        }

        $nextRun = CronExpression::factory($validated['cron_expression'])
            ->setTimezone($validated['timezone'] ?? 'UTC')
            ->getNextRunDate();

        $trigger = WorkflowTrigger::where('workflow_id', $workflow->id)
            ->where('type', TriggerType::Scheduled->value)
            ->first();

        return $this->model->create([
            ...$validated,
            'school_id' => $workflow->school_id,
            'workflow_id' => $workflow->id,
            'trigger_id' => $trigger?->id,
            'timezone' => $validated['timezone'] ?? 'UTC',
            'next_run_at' => $nextRun,
        ]);
    }

    public function updateSchedule(WorkflowSchedule $schedule, array $data): WorkflowSchedule
    {
        $validated = \Validator::make($data, [
            'name' => ['sometimes', 'required', 'string', 'max:200'],
            'cron_expression' => ['sometimes', 'required', 'string', 'max:100'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
            'payload' => ['nullable', 'array'],
            'conditions' => ['nullable', 'array'],
        ])->validate();

        if (isset($validated['cron_expression'])) {
            if (! CronExpression::isValid($validated['cron_expression'])) {
                throw ValidationException::withMessages([
                    'cron_expression' => 'Invalid cron expression.',
                ]);
            }

            $nextRun = CronExpression::factory($validated['cron_expression'])
                ->setTimezone($validated['timezone'] ?? $schedule->timezone)
                ->getNextRunDate();

            $validated['next_run_at'] = $nextRun;
        }

        $schedule->update($validated);

        return $schedule->fresh();
    }

    public function enable(WorkflowSchedule $schedule): WorkflowSchedule
    {
        $schedule->update(['is_active' => true]);

        return $schedule->fresh();
    }

    public function disable(WorkflowSchedule $schedule): WorkflowSchedule
    {
        $schedule->update(['is_active' => false]);

        return $schedule->fresh();
    }

    public function delete(WorkflowSchedule $schedule): void
    {
        $schedule->delete();
    }

    public function getDueSchedules(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->active()
            ->where(function ($query) {
                $query->whereNull('next_run_at')
                    ->orWhere('next_run_at', '<=', now());
            })
            ->get();
    }

    public function processSchedule(WorkflowSchedule $schedule): void
    {
        $workflow = Workflow::find($schedule->workflow_id);

        if (! $workflow || ! $workflow->is_active) {
            $schedule->update([
                'total_runs' => $schedule->total_runs + 1,
                'failed_runs' => $schedule->failed_runs + 1,
            ]);

            return;
        }

        ProcessScheduledWorkflow::dispatch($schedule->id, $workflow->id, $schedule->payload);

        $nextRun = CronExpression::factory($schedule->cron_expression)
            ->setTimezone($schedule->timezone)
            ->getNextRunDate();

        $schedule->update([
            'last_run_at' => now(),
            'next_run_at' => $nextRun,
            'total_runs' => $schedule->total_runs + 1,
        ]);
    }

    public function findById(string $id): WorkflowSchedule
    {
        return $this->model->with(['workflow', 'trigger'])->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['workflow_id'])) {
            $query->where('workflow_id', $filters['workflow_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }
}
