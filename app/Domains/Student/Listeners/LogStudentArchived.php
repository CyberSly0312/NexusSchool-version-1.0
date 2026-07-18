<?php

declare(strict_types=1);

namespace App\Domains\Student\Listeners;

use App\Domains\Student\Events\StudentArchived;
use Illuminate\Support\Facades\Log;

class LogStudentArchived
{
    public function handle(StudentArchived $event): void
    {
        Log::info('Student archived', [
            'student_id' => $event->student->id,
            'student_number' => $event->student->student_number,
            'school_id' => $event->student->school_id,
        ]);
    }
}
