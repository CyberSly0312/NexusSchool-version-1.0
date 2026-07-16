<?php

namespace App\Domains\Student\Listeners;

use App\Domains\Student\Events\StudentActivated;
use Illuminate\Support\Facades\Log;

class LogStudentActivated
{
    public function handle(StudentActivated $event): void
    {
        Log::info('Student activated', [
            'student_id' => $event->student->id,
            'student_number' => $event->student->student_number,
            'school_id' => $event->student->school_id,
        ]);
    }
}
