<?php

namespace App\Domains\Student\Listeners;

use App\Domains\Student\Events\StudentCreated;
use Illuminate\Support\Facades\Log;

class LogStudentCreated
{
    public function handle(StudentCreated $event): void
    {
        Log::info('Student created', [
            'student_id' => $event->student->id,
            'student_number' => $event->student->student_number,
            'name' => $event->student->full_name,
            'school_id' => $event->student->school_id,
        ]);
    }
}
