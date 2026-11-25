<?php

declare(strict_types=1);

namespace App\Domains\Role\Events;

use App\Domains\Role\DTOs\RoleChangeCommand;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoleChangeRejected
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly User $actor,
        public readonly RoleChangeCommand $command,
        public readonly string $reason,
    ) {
    }
}
