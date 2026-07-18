<?php

declare(strict_types=1);

namespace App\Domains\Transport\Events;

use App\Models\RouteStop;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StopRemoved
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly RouteStop $stop,
    ) {
    }
}
