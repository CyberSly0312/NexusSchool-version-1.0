<?php

declare(strict_types=1);

namespace App\Jobs\Workflow;

use App\Domains\Workflow\Services\ExecutionService;
use App\Domains\Workflow\Services\RuleEngineService;
use App\Models\WorkflowExecution;
use App\Models\WorkflowVersion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExecuteWorkflowNode implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 5;

    public int $timeout = 120;

    public function __construct(
        public string $executionId,
        public string $nodeId,
    ) {
    }

    public function handle(RuleEngineService $ruleEngine, ExecutionService $executionService): void
    {
        $execution = WorkflowExecution::findOrFail($this->executionId);

        if ($execution->status->isTerminal()) {
            return;
        }

        $version = WorkflowVersion::find($execution->workflow_version_id);
        if (! $version) {
            $executionService->failExecution($execution, 'Workflow version not found.');

            return;
        }

        $nodes = $version->nodes ?? [];
        $node = collect($nodes)->firstWhere('id', $this->nodeId);

        if (! $node) {
            $executionService->failExecution($execution, "Node {$this->nodeId} not found in workflow definition.");

            return;
        }

        $execution->update(['current_node_id' => $this->nodeId]);

        $result = $ruleEngine->evaluateNode($node, $execution);

        match ($result['action'] ?? 'skip') {
            'proceed' => $this->proceedToNext($result, $execution, $ruleEngine),
            'complete' => $executionService->completeExecution($execution),
            'wait' => $execution->update(['status' => \App\Domains\Workflow\Enums\ExecutionStatus::Paused->value]),
            default => null,
        };
    }

    private function proceedToNext(array $result, WorkflowExecution $execution, RuleEngineService $ruleEngine): void
    {
        $nextNodeId = $result['next'] ?? null;

        if ($nextNodeId) {
            self::dispatch($execution->id, $nextNodeId);
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('Workflow Node Execution Failed', [
            'execution_id' => $this->executionId,
            'node_id' => $this->nodeId,
            'error' => $exception->getMessage(),
        ]);
    }
}
