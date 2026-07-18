<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Events;

use App\Models\Guardian;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GuardianActivated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Guardian $guardian,
    ) {
    }
}
