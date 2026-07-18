<?php

declare(strict_types=1);

namespace App\Jobs\System;

use App\Domains\System\Services\HealthCheckService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessHealthCheck implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public ?string $service = null,
    ) {
    }

    public function handle(HealthCheckService $healthCheckService): void
    {
        if ($this->service) {
            $healthCheckService->runSingleCheck($this->service);
        } else {
            $healthCheckService->runAllChecks();
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('Health Check Failed', [
            'service' => $this->service,
            'error' => $exception->getMessage(),
        ]);
    }
}
