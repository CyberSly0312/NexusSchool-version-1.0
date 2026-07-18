<?php

declare(strict_types=1);

namespace App\Domains\System\Events;

use App\Models\SystemHealth;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HealthCheckFailed
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly SystemHealth $health,
        public readonly ?string $message = null,
    ) {
    }
}
