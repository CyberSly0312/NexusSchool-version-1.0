<?php

declare(strict_types=1);

namespace App\Domains\Transport\Events;

use App\Models\FuelLog;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FuelLogged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly FuelLog $fuelLog,
    ) {
    }
}
