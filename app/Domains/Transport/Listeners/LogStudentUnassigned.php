<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\StudentUnassigned;
use Illuminate\Support\Facades\Log;

class LogStudentUnassigned
{
    public function handle(StudentUnassigned $event): void
    {
        Log::info('StudentUnassigned fired', [
            'assignment_id' => $event->assignment->id,
            'student_id' => $event->assignment->student_id,
            'school_id' => $event->assignment->school_id,
        ]);
    }
}
