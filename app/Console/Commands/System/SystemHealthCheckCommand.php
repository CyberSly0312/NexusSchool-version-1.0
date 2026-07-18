<?php

declare(strict_types=1);

namespace App\Console\Commands\System;

use App\Domains\System\Services\HealthCheckService;
use Illuminate\Console\Command;

class SystemHealthCheckCommand extends Command
{
    protected $signature = 'system:health-check {--service= : Specific service to check}';

    protected $description = 'Run system health checks';

    public function handle(HealthCheckService $healthCheckService): int
    {
        $service = $this->option('service');

        $this->info('Running health checks...');

        if ($service) {
            $result = $healthCheckService->runSingleCheck($service);
            $time = $result['response_time_ms'] ?? 0;
            $this->line("  {$service}: {$result['status']} ({$time}ms)");

            if (isset($result['message'])) {
                $this->line("  Message: {$result['message']}");
            }
        } else {
            $results = $healthCheckService->runAllChecks();

            foreach ($results as $serviceName => $result) {
                $status = $result['status'] ?? 'unknown';
                $time = $result['response_time_ms'] ?? 0;
                $icon = match ($status) {
                    'healthy' => '<info>OK</info>',
                    'degraded' => '<comment>WARN</comment>',
                    'unhealthy' => '<error>FAIL</error>',
                    default => '<fg=gray>?</>',
                };
                $this->line("  {$serviceName}: {$icon} ({$time}ms)");
            }
        }

        return self::SUCCESS;
    }
}
