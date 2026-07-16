<?php

namespace App\Domains\Permission\Listeners;

use App\Domains\Permission\Events\PermissionCreated;
use Illuminate\Support\Facades\Log;

class LogPermissionCreated
{
    public function handle(PermissionCreated $event): void
    {
        Log::info('Permission created', [
            'permission_id' => $event->permission->id,
            'name' => $event->permission->name,
            'guard_name' => $event->permission->guard_name,
        ]);
    }
}
