<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\DriverAssigned;
use Illuminate\Support\Facades\Log;

class LogDriverAssigned
{
    public function handle(DriverAssigned $event): void
    {
        Log::info('DriverAssigned fired', [
            'driver_id' => $event->driver->id,
            'school_id' => $event->driver->school_id,
        ]);
    }
}
