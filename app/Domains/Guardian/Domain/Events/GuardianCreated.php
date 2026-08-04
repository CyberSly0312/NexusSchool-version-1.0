<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Events;

use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GuardianCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Guardian $guardian,
        public readonly ?User $actor = null,
    ) {
    }
}
