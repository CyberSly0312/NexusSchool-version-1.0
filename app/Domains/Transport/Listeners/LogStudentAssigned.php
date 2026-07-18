<?php

declare(strict_types=1);

namespace App\Domains\Transport\Listeners;

use App\Domains\Transport\Events\StudentAssigned;
use Illuminate\Support\Facades\Log;

class LogStudentAssigned
{
    public function handle(StudentAssigned $event): void
    {
        Log::info('StudentAssigned fired', [
            'assignment_id' => $event->assignment->id,
            'student_id' => $event->assignment->student_id,
            'school_id' => $event->assignment->school_id,
        ]);
    }
}
