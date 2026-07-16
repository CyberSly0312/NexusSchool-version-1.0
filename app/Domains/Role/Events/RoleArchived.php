<?php

namespace App\Domains\Role\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\Permission\Models\Role;

class RoleArchived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Role $role,
    ) {}
}
