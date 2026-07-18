<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\MaintenanceScheduled;
use Illuminate\Support\Facades\Log;

class LogMaintenanceScheduled
{
    public function handle(MaintenanceScheduled $event): void
    {
        Log::info('MaintenanceScheduled fired', [
            'maintenance_id' => $event->maintenance->id,
            'vehicle_id' => $event->maintenance->vehicle_id,
            'school_id' => $event->maintenance->school_id,
        ]);
    }
}
