<?php

declare(strict_types=1);

namespace App\Domains\Role\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\Permission\Models\Role;

class RoleUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Role $role,
    ) {
    }
}
