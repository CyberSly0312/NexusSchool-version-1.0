<?php

namespace App\Domains\Department\Listeners;

use App\Domains\Department\Events\DepartmentUpdated;
use Illuminate\Support\Facades\Log;

class LogDepartmentUpdated
{
    public function handle(DepartmentUpdated $event): void
    {
        Log::info('Department updated', [
            'department_id' => $event->department->id,
            'name' => $event->department->name,
            'status' => $event->department->status->value,
        ]);
    }
}
