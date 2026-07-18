<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\RouteArchived;
use Illuminate\Support\Facades\Log;

class LogRouteArchived
{
    public function handle(RouteArchived $event): void
    {
        Log::info('RouteArchived fired', [
            'route_id' => $event->route->id,
            'school_id' => $event->route->school_id,
        ]);
    }
}
