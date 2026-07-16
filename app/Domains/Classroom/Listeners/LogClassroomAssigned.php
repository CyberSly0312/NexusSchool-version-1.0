<?php

namespace App\Domains\Classroom\Listeners;

use App\Domains\Classroom\Events\ClassroomAssigned;
use Illuminate\Support\Facades\Log;

class LogClassroomAssigned
{
    public function handle(ClassroomAssigned $event): void
    {
        Log::info('Classroom assigned to academic class', [
            'classroom_id' => $event->classroom->id,
            'academic_class_id' => $event->academicClass->id,
            'capacity' => $event->classroom->capacity,
        ]);
    }
}
