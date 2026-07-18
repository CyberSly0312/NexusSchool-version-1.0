<?php

declare(strict_types=1);

namespace App\Domains\Transport\Events;

use App\Models\Driver;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Driver $driver,
    ) {
    }
}
