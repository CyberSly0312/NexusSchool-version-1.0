<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Models\SystemHealth;
use Illuminate\Console\Command;

class SystemCleanupCommand extends Command
{
    protected $signature = 'system:cleanup {--days=90 : Number of days to retain} {--dry-run : Show what would be deleted}';

    protected $description = 'Clean up old system logs and records';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoff = now()->subDays($days);

        $auditCount = AuditLog::where('created_at', '<', $cutoff)->count();
        $activityCount = ActivityLog::where('created_at', '<', $cutoff)->count();
        $healthCount = SystemHealth::where('created_at', '<', $cutoff)->count();

        if ($dryRun) {
            $this->info("Dry run — would delete records older than {$cutoff->toDateString()}:");
            $this->line("  AuditLog: {$auditCount}");
            $this->line("  ActivityLog: {$activityCount}");
            $this->line("  SystemHealth: {$healthCount}");

            return self::SUCCESS;
        }

        AuditLog::where('created_at', '<', $cutoff)->delete();
        ActivityLog::where('created_at', '<', $cutoff)->delete();
        SystemHealth::where('created_at', '<', $cutoff)->delete();

        $total = $auditCount + $activityCount + $healthCount;

        $this->info("Deleted {$total} records older than {$cutoff->toDateString()}.");

        return self::SUCCESS;
    }
}
