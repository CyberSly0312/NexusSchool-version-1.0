<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\RouteUpdated;
use Illuminate\Support\Facades\Log;

class LogRouteUpdated
{
    public function handle(RouteUpdated $event): void
    {
        Log::info('RouteUpdated fired', [
            'route_id' => $event->route->id,
            'school_id' => $event->route->school_id,
        ]);
    }
}
