<?php

declare(strict_types=1);

namespace App\Domains\Permission\Listeners;

use App\Domains\Permission\Events\PermissionRevokedFromRole;
use Illuminate\Support\Facades\Log;

class LogPermissionRevokedFromRole
{
    public function handle(PermissionRevokedFromRole $event): void
    {
        Log::info('Permission revoked from role', [
            'permission_id' => $event->permission->id,
            'permission_name' => $event->permission->name,
            'role_id' => $event->role->id,
            'role_name' => $event->role->name,
        ]);
    }
}
