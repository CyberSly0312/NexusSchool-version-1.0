<?php

namespace App\Domains\Permission\Listeners;

use App\Domains\Permission\Events\PermissionAssignedToRole;
use Illuminate\Support\Facades\Log;

class LogPermissionAssignedToRole
{
    public function handle(PermissionAssignedToRole $event): void
    {
        Log::info('Permission assigned to role', [
            'permission_id' => $event->permission->id,
            'permission_name' => $event->permission->name,
            'role_id' => $event->role->id,
            'role_name' => $event->role->name,
        ]);
    }
}
