<?php

declare(strict_types=1);

namespace App\Domains\System\Services;

use App\Domains\System\Events\HealthCheckFailed;
use App\Models\SystemHealth;

class HealthCheckService
{
    public function __construct(
        protected MonitoringService $monitoringService = new MonitoringService(),
        protected SystemHealth $model = new SystemHealth(),
    ) {
    }

    public function runAllChecks(): array
    {
        $results = [];

        $results['database'] = $this->monitoringService->checkDatabase();
        $results['cache'] = $this->monitoringService->checkCache();
        $results['queue'] = $this->monitoringService->checkQueue();
        $results['storage'] = $this->monitoringService->checkStorage();
        $results['mail'] = $this->monitoringService->checkMail();
        $results['scheduler'] = $this->monitoringService->checkScheduler();

        foreach ($results as $service => $result) {
            if (($result['status'] ?? 'unknown') === 'unhealthy') {
                HealthCheckFailed::dispatch(
                    $this->model->where('service', $service)->latest()->first(),
                    $result['message'] ?? "{$service} health check failed"
                );
            }
        }

        return $results;
    }

    public function runSingleCheck(string $service): array
    {
        return match ($service) {
            'database' => $this->monitoringService->checkDatabase(),
            'cache' => $this->monitoringService->checkCache(),
            'queue' => $this->monitoringService->checkQueue(),
            'storage' => $this->monitoringService->checkStorage(),
            'mail' => $this->monitoringService->checkMail(),
            'scheduler' => $this->monitoringService->checkScheduler(),
            default => ['status' => 'unknown', 'message' => "Unknown service: {$service}"],
        };
    }

    public function getStatus(): array
    {
        $overall = $this->monitoringService->getOverallStatus();
        $checks = $this->monitoringService->getLatestHealthChecks();

        return [
            'overall' => $overall,
            'checks' => $checks,
            'checked_at' => now()->toIso8601String(),
        ];
    }

    public function getSystemMetrics(): array
    {
        $memoryUsage = 0;

        if (function_exists('memory_get_usage')) {
            $memoryUsage = (int) ((memory_get_usage(true) / memory_get_usage(true)) * 100);
        }

        return [
            'memory_usage_percent' => $memoryUsage,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'CLI',
            'disk_usage' => $this->getDiskUsage(),
        ];
    }

    private function getDiskUsage(): array
    {
        $total = disk_total_space(storage_path());
        $free = disk_free_space(storage_path());

        return [
            'total_bytes' => $total,
            'free_bytes' => $free,
            'used_bytes' => $total - $free,
            'usage_percent' => (int) ((($total - $free) / $total) * 100),
        ];
    }
}
