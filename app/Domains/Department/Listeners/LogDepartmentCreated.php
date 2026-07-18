<?php

declare(strict_types=1);

namespace App\Domains\Department\Listeners;

use App\Domains\Department\Events\DepartmentCreated;
use Illuminate\Support\Facades\Log;

class LogDepartmentCreated
{
    public function handle(DepartmentCreated $event): void
    {
        Log::info('Department created', [
            'department_id' => $event->department->id,
            'name' => $event->department->name,
            'code' => $event->department->code,
            'school_id' => $event->department->school_id,
        ]);
    }
}
