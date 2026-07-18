<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\TripCompleted;
use Illuminate\Support\Facades\Log;

class LogTripCompleted
{
    public function handle(TripCompleted $event): void
    {
        Log::info('TripCompleted fired', [
            'trip_id' => $event->trip->id,
            'school_id' => $event->trip->school_id,
        ]);
    }
}
