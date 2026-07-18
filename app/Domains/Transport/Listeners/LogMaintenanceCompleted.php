<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\MaintenanceCompleted;
use Illuminate\Support\Facades\Log;

class LogMaintenanceCompleted
{
    public function handle(MaintenanceCompleted $event): void
    {
        Log::info('MaintenanceCompleted fired', [
            'maintenance_id' => $event->maintenance->id,
            'vehicle_id' => $event->maintenance->vehicle_id,
            'school_id' => $event->maintenance->school_id,
        ]);
    }
}
