<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\System\Services\MaintenanceService;
use App\Models\MaintenanceWindow;
use Illuminate\Console\Command;

class SystemMaintenanceCommand extends Command
{
    protected $signature = 'system:maintenance {action : start|end|status} {--id= : Maintenance window ID}';

    protected $description = 'Manage maintenance windows';

    public function handle(MaintenanceService $maintenanceService): int
    {
        $action = $this->argument('action');

        return match ($action) {
            'start' => $this->startWindow($maintenanceService),
            'end' => $this->endWindow($maintenanceService),
            'status' => $this->showStatus($maintenanceService),
            default => $this->error('Invalid action. Use: start, end, or status.'),
        };
    }

    private function startWindow(MaintenanceService $maintenanceService): int
    {
        $id = $this->option('id');

        if (!$id) {
            $this->error('--id is required for start action.');

            return self::FAILURE;
        }

        $window = MaintenanceWindow::findOrFail($id);
        $maintenanceService->start($window);

        $this->info("Maintenance window started: {$window->name}");

        return self::SUCCESS;
    }

    private function endWindow(MaintenanceService $maintenanceService): int
    {
        $id = $this->option('id');

        if (!$id) {
            $this->error('--id is required for end action.');

            return self::FAILURE;
        }

        $window = MaintenanceWindow::findOrFail($id);
        $maintenanceService->end($window);

        $this->info("Maintenance window ended: {$window->name}");

        return self::SUCCESS;
    }

    private function showStatus(MaintenanceService $maintenanceService): int
    {
        $isActive = $maintenanceService->isActive();
        $isReadonly = $maintenanceService->isReadonly();

        $this->line('Maintenance active: ' . ($isActive ? 'Yes' : 'No'));
        $this->line('System readonly: ' . ($isReadonly ? 'Yes' : 'No'));

        return self::SUCCESS;
    }
}
