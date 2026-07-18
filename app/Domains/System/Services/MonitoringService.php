<?php

declare(strict_types=1);

namespace App\Domains\System\Services;

use App\Domains\System\DTOs\SystemHealthData;
use App\Models\SystemHealth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class MonitoringService
{
    public function __construct(
        protected SystemHealth $model = new SystemHealth(),
    ) {
    }

    public function checkDatabase(): array
    {
        $start = microtime(true);

        try {
            DB::connection()->getPdo();
            $responseTime = (int) ((microtime(true) - $start) * 1000);

            return $this->recordCheck('database', 'healthy', $responseTime, 'Database connection successful');
        } catch (\Throwable $e) {
            $responseTime = (int) ((microtime(true) - $start) * 1000);

            return $this->recordCheck('database', 'unhealthy', $responseTime, $e->getMessage());
        }
    }

    public function checkCache(): array
    {
        $start = microtime(true);

        try {
            $testKey = 'health_check_' . time();
            Cache::put($testKey, 'ok', 10);
            $result = Cache::get($testKey);
            Cache::forget($testKey);
            $responseTime = (int) ((microtime(true) - $start) * 1000);

            return $this->recordCheck('cache', $result === 'ok' ? 'healthy' : 'degraded', $responseTime, 'Cache check completed');
        } catch (\Throwable $e) {
            $responseTime = (int) ((microtime(true) - $start) * 1000);

            return $this->recordCheck('cache', 'unhealthy', $responseTime, $e->getMessage());
        }
    }

    public function checkQueue(): array
    {
        $start = microtime(true);

        try {
            $queueConnection = config('queue.default');
            $isOpen = Queue::connection($queueConnection)->getQueue('default') !== null;
            $responseTime = (int) ((microtime(true) - $start) * 1000);

            return $this->recordCheck('queue', 'healthy', $responseTime, "Queue connection [{$queueConnection}] is operational");
        } catch (\Throwable $e) {
            $responseTime = (int) ((microtime(true) - $start) * 1000);

            return $this->recordCheck('queue', 'unhealthy', $responseTime, $e->getMessage());
        }
    }

    public function checkStorage(): array
    {
        $start = microtime(true);

        try {
            $diskTotal = disk_total_space(storage_path());
            $diskFree = disk_free_space(storage_path());
            $diskUsed = $diskTotal - $diskFree;
            $usagePercent = (int) (($diskUsed / $diskTotal) * 100);
            $responseTime = (int) ((microtime(true) - $start) * 1000);

            $status = $usagePercent > 90 ? 'unhealthy' : ($usagePercent > 80 ? 'degraded' : 'healthy');

            return $this->recordCheck('storage', $status, $responseTime, "Disk usage: {$usagePercent}%", [
                'disk_used_bytes' => $diskUsed,
                'disk_total_bytes' => $diskTotal,
                'cpu_usage_percent' => null,
                'memory_usage_percent' => $usagePercent,
            ]);
        } catch (\Throwable $e) {
            $responseTime = (int) ((microtime(true) - $start) * 1000);

            return $this->recordCheck('storage', 'unhealthy', $responseTime, $e->getMessage());
        }
    }

    public function checkMail(): array
    {
        $start = microtime(true);

        try {
            $mailDriver = config('mail.default');
            $responseTime = (int) ((microtime(true) - $start) * 1000);

            return $this->recordCheck('mail', 'healthy', $responseTime, "Mail driver [{$mailDriver}] configured");
        } catch (\Throwable $e) {
            $responseTime = (int) ((microtime(true) - $start) * 1000);

            return $this->recordCheck('mail', 'unhealthy', $responseTime, $e->getMessage());
        }
    }

    public function checkScheduler(): array
    {
        $start = microtime(true);

        try {
            $lastRun = Cache::get('scheduler_last_run');
            $responseTime = (int) ((microtime(true) - $start) * 1000);

            $status = $lastRun && now()->diffInMinutes(\Carbon\Carbon::parse($lastRun)) > 5 ? 'degraded' : 'healthy';

            return $this->recordCheck('scheduler', $status, $responseTime, $lastRun ? 'Last scheduler run: ' . $lastRun : 'Scheduler status unknown');
        } catch (\Throwable $e) {
            $responseTime = (int) ((microtime(true) - $start) * 1000);

            return $this->recordCheck('scheduler', 'unhealthy', $responseTime, $e->getMessage());
        }
    }

    public function getLatestHealthChecks(): array
    {
        return $this->model->selectRaw('service, status, response_time_ms, message, checked_at')
            ->orderBy('checked_at', 'desc')
            ->groupBy('service')
            ->get()
            ->keyBy('service')
            ->toArray();
    }

    public function getHealthHistory(string $service, int $hours = 24): \Illuminate\Support\Collection
    {
        return $this->model
            ->where('service', $service)
            ->where('checked_at', '>=', now()->subHours($hours))
            ->orderBy('checked_at', 'desc')
            ->get();
    }

    public function getOverallStatus(): string
    {
        $latest = $this->getLatestHealthChecks();

        if (empty($latest)) {
            return 'unknown';
        }

        $statuses = array_column($latest, 'status');

        if (in_array('unhealthy', $statuses)) {
            return 'unhealthy';
        }

        if (in_array('degraded', $statuses)) {
            return 'degraded';
        }

        return 'healthy';
    }

    private function recordCheck(string $service, string $status, int $responseTimeMs, ?string $message = null, array $extra = []): array
    {
        $data = array_merge([
            'service' => $service,
            'status' => $status,
            'response_time_ms' => $responseTimeMs,
            'message' => $message,
        ], $extra);

        $record = $this->model->create($data);

        return SystemHealthData::create($record->toArray())->toArray();
    }
}
