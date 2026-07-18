<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\System\ProcessHealthCheck;
use Illuminate\Console\Command;

class SystemHealthCheckCommand extends Command
{
    protected $signature = 'system:health-check {--service= : Check a specific service only}';

    protected $description = 'Run system health checks';

    public function handle(): int
    {
        $service = $this->option('service');

        ProcessHealthCheck::dispatch($service);

        $this->info('Health check dispatched.' . ($service ? " Checking: {$service}" : ' Checking all services.'));

        return self::SUCCESS;
    }
}
