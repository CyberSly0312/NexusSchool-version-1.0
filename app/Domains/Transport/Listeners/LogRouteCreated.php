<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\RouteCreated;
use Illuminate\Support\Facades\Log;

class LogRouteCreated
{
    public function handle(RouteCreated $event): void
    {
        Log::info('RouteCreated fired', [
            'route_id' => $event->route->id,
            'school_id' => $event->route->school_id,
        ]);
    }
}
