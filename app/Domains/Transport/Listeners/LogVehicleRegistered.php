<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\VehicleRegistered;
use Illuminate\Support\Facades\Log;

class LogVehicleRegistered
{
    public function handle(VehicleRegistered $event): void
    {
        Log::info('VehicleRegistered fired', [
            'vehicle_id' => $event->vehicle->id,
            'school_id' => $event->vehicle->school_id,
        ]);
    }
}
