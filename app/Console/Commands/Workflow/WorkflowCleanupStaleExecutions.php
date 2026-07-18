<?php

declare(strict_types=1);

namespace App\Console\Commands\Workflow;

use App\Domains\Workflow\Enums\ExecutionStatus;
use App\Models\WorkflowExecution;
use Illuminate\Console\Command;

class WorkflowCleanupStaleExecutions extends Command
{
    protected $signature = 'workflow:cleanup-stale {--hours=24 : Hours after which a running execution is considered stale}';

    protected $description = 'Mark stale running workflow executions as timed out';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $threshold = now()->subHours($hours);

        $staleCount = WorkflowExecution::where('status', ExecutionStatus::Running->value)
            ->where('started_at', '<', $threshold)
            ->update(['status' => ExecutionStatus::TimedOut->value, 'completed_at' => now()]);

        $this->info("Marked {$staleCount} stale execution(s) as timed out.");

        return self::SUCCESS;
    }
}
