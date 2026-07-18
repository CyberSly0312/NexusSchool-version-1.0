<?php

declare(strict_types=1);

namespace App\Domains\Permission\Listeners;

use App\Domains\Permission\Events\PermissionArchived;
use Illuminate\Support\Facades\Log;

class LogPermissionArchived
{
    public function handle(PermissionArchived $event): void
    {
        Log::info('Permission archived', [
            'permission_id' => $event->permission->id,
            'name' => $event->permission->name,
        ]);
    }
}
