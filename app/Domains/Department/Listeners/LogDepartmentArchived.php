<?php

declare(strict_types=1);

namespace App\Domains\Department\Listeners;

use App\Domains\Department\Events\DepartmentArchived;
use Illuminate\Support\Facades\Log;

class LogDepartmentArchived
{
    public function handle(DepartmentArchived $event): void
    {
        Log::info('Department archived', [
            'department_id' => $event->department->id,
            'name' => $event->department->name,
            'school_id' => $event->department->school_id,
        ]);
    }
}
