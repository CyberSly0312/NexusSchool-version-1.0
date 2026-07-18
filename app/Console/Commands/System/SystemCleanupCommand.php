<?php

declare(strict_types=1);

namespace App\Console\Commands\System;

use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Models\SystemHealth;
use Illuminate\Console\Command;

class SystemCleanupCommand extends Command
{
    protected $signature = 'system:cleanup {--days=90 : Remove records older than this}';

    protected $description = 'Clean up old system records';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $threshold = now()->subDays($days);

        $auditCount = AuditLog::where('created_at', '<', $threshold)->count();
        AuditLog::where('created_at', '<', $threshold)->delete();

        $activityCount = ActivityLog::where('created_at', '<', $threshold)->count();
        ActivityLog::where('created_at', '<', $threshold)->delete();

        $healthCount = SystemHealth::where('checked_at', '<', $threshold)->delete();

        $this->info('Cleanup completed:');
        $this->line("  Audit logs removed: {$auditCount}");
        $this->line("  Activity logs removed: {$activityCount}");
        $this->line("  Health records removed: {$healthCount}");

        return self::SUCCESS;
    }
}
