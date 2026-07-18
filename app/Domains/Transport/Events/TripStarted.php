<?php

declare(strict_types=1);

namespace App\Domains\Transport\Events;

use App\Models\Trip;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripStarted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Trip $trip,
    ) {
    }
}
