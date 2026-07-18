<?php

declare(strict_types=1);

namespace App\Console\Commands\System;

use App\Domains\System\Services\MaintenanceService;
use Illuminate\Console\Command;

class SystemMaintenanceCommand extends Command
{
    protected $signature = 'system:maintenance {action=start|end} {--id= : Maintenance window ID}';

    protected $description = 'Manage maintenance windows';

    public function handle(MaintenanceService $maintenanceService): int
    {
        $action = $this->argument('action');
        $id = $this->option('id');

        if (! $id) {
            $this->error('Please provide a maintenance window ID with --id=');

            return self::FAILURE;
        }

        $window = $maintenanceService->findById($id);

        match ($action) {
            'start' => $maintenanceService->start($window),
            'end' => $maintenanceService->end($window),
            default => $this->error("Unknown action: {$action}"),
        };

        $this->info("Maintenance window {$action}ed successfully.");

        return self::SUCCESS;
    }
}
