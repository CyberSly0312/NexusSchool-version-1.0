<?php

declare(strict_types=1);

namespace App\Jobs\Workflow;

use App\Domains\Workflow\Services\ExecutionService;
use App\Models\Workflow;
use App\Models\WorkflowSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessScheduledWorkflow implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public string $scheduleId,
        public string $workflowId,
        public ?array $payload = null,
    ) {
    }

    public function handle(ExecutionService $executionService): void
    {
        $schedule = WorkflowSchedule::find($this->scheduleId);
        $workflow = Workflow::find($this->workflowId);

        if (! $workflow || ! $workflow->is_active) {
            return;
        }

        $data = [
            'school_id' => $workflow->school_id,
            'workflow_id' => $workflow->id,
            'context' => [
                'trigger' => 'scheduled',
                'schedule_id' => $this->scheduleId,
                'payload' => $this->payload,
            ],
            'initiated_by' => null,
            'initiated_by_type' => 'system',
        ];

        $executionService->startExecution($data);
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('Scheduled Workflow Processing Failed', [
            'schedule_id' => $this->scheduleId,
            'workflow_id' => $this->workflowId,
            'error' => $exception->getMessage(),
        ]);
    }
}
