<?php

namespace App\Domains\Permission\Listeners;

use App\Domains\Permission\Events\PermissionUpdated;
use Illuminate\Support\Facades\Log;

class LogPermissionUpdated
{
    public function handle(PermissionUpdated $event): void
    {
        Log::info('Permission updated', [
            'permission_id' => $event->permission->id,
            'name' => $event->permission->name,
        ]);
    }
}
