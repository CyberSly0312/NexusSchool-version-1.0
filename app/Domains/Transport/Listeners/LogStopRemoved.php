<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\StopRemoved;
use Illuminate\Support\Facades\Log;

class LogStopRemoved
{
    public function handle(StopRemoved $event): void
    {
        Log::info('StopRemoved fired', [
            'stop_id' => $event->stop->id,
            'route_id' => $event->stop->route_id,
            'school_id' => $event->stop->school_id,
        ]);
    }
}
