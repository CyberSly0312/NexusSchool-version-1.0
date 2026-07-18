<?php

declare(strict_types=1);

namespace App\Domains\System\Events;

use App\Models\MaintenanceWindow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaintenanceEnded
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly MaintenanceWindow $window,
    ) {
    }
}
