<?php

namespace App\Domains\StudentEnrollment\Listeners;

use App\Domains\StudentEnrollment\Events\StudentEnrolled;
use Illuminate\Support\Facades\Log;

class LogStudentEnrolled
{
    public function handle(StudentEnrolled $event): void
    {
        Log::info('Student enrolled', [
            'enrollment_id' => $event->enrollment->id,
            'student_id' => $event->enrollment->student_id,
            'enrollment_number' => $event->enrollment->enrollment_number,
            'school_id' => $event->enrollment->school_id,
        ]);
    }
}
