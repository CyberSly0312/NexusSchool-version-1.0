<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\TripScheduled;
use Illuminate\Support\Facades\Log;

class LogTripScheduled
{
    public function handle(TripScheduled $event): void
    {
        Log::info('TripScheduled fired', [
            'trip_id' => $event->trip->id,
            'school_id' => $event->trip->school_id,
        ]);
    }
}
