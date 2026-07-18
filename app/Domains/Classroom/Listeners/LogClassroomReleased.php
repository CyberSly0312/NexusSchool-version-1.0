<?php

declare(strict_types=1);

namespace App\Domains\Classroom\Listeners;

use App\Domains\Classroom\Events\ClassroomReleased;
use Illuminate\Support\Facades\Log;

class LogClassroomReleased
{
    public function handle(ClassroomReleased $event): void
    {
        Log::info('Classroom released from academic class', [
            'classroom_id' => $event->classroom->id,
            'academic_class_id' => $event->academicClass->id,
        ]);
    }
}
