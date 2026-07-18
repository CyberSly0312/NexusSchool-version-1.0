<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\DriverUpdated;
use Illuminate\Support\Facades\Log;

class LogDriverUpdated
{
    public function handle(DriverUpdated $event): void
    {
        Log::info('DriverUpdated fired', [
            'driver_id' => $event->driver->id,
            'school_id' => $event->driver->school_id,
        ]);
    }
}
