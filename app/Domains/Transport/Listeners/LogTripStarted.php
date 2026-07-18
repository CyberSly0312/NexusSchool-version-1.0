<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\TripStarted;
use Illuminate\Support\Facades\Log;

class LogTripStarted
{
    public function handle(TripStarted $event): void
    {
        Log::info('TripStarted fired', [
            'trip_id' => $event->trip->id,
            'school_id' => $event->trip->school_id,
        ]);
    }
}
