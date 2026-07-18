<?php

declare(strict_types=1);

namespace App\Domains\Transport\Events;

use App\Models\Vehicle;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VehicleUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Vehicle $vehicle,
    ) {
    }
}
