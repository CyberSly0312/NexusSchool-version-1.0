<?php

declare(strict_types=1);

namespace App\Domains\Transport\Events;

use App\Models\VehicleMaintenance;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaintenanceCompleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly VehicleMaintenance $maintenance,
    ) {
    }
}
