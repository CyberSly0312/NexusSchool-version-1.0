<?php

declare(strict_types=1);

namespace App\Domains\Transport\Events;

use App\Models\Route;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RouteUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Route $route,
    ) {
    }
}
