<?php

namespace App\Domains\Role\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\Permission\Models\Role;

class RoleRevokedFromUser
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Role $role,
    ) {}
}
