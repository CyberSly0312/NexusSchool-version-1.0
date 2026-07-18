<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\FuelLogged;
use Illuminate\Support\Facades\Log;

class LogFuelLogged
{
    public function handle(FuelLogged $event): void
    {
        Log::info('FuelLogged fired', [
            'fuel_log_id' => $event->fuelLog->id,
            'vehicle_id' => $event->fuelLog->vehicle_id,
            'school_id' => $event->fuelLog->school_id,
        ]);
    }
}
