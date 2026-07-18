<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\VehicleUpdated;
use Illuminate\Support\Facades\Log;

class LogVehicleUpdated
{
    public function handle(VehicleUpdated $event): void
    {
        Log::info('VehicleUpdated fired', [
            'vehicle_id' => $event->vehicle->id,
            'school_id' => $event->vehicle->school_id,
        ]);
    }
}
