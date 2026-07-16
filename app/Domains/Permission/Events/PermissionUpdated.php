<?php

namespace App\Domains\Permission\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\Permission\Models\Permission;

class PermissionUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Permission $permission,
    ) {}
}
