<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\StopAdded;
use Illuminate\Support\Facades\Log;

class LogStopAdded
{
    public function handle(StopAdded $event): void
    {
        Log::info('StopAdded fired', [
            'stop_id' => $event->stop->id,
            'route_id' => $event->stop->route_id,
            'school_id' => $event->stop->school_id,
        ]);
    }
}
