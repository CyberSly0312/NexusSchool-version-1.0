<?php

declare(strict_types=1);

namespace App\Domains\Classroom\Listeners;

use App\Domains\Classroom\Events\ClassroomArchived;
use Illuminate\Support\Facades\Log;

class LogClassroomArchived
{
    public function handle(ClassroomArchived $event): void
    {
        Log::info('Classroom archived', [
            'classroom_id' => $event->classroom->id,
            'name' => $event->classroom->name,
            'school_id' => $event->classroom->school_id,
        ]);
    }
}
