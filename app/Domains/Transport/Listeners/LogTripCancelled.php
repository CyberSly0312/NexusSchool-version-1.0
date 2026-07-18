<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\TripCancelled;
use Illuminate\Support\Facades\Log;

class LogTripCancelled
{
    public function handle(TripCancelled $event): void
    {
        Log::info('TripCancelled fired', [
            'trip_id' => $event->trip->id,
            'school_id' => $event->trip->school_id,
        ]);
    }
}
