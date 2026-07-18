<?php

declare(strict_types=1);

namespace App\Domains\Student\Listeners;

use App\Domains\Student\Events\StudentUpdated;
use Illuminate\Support\Facades\Log;

class LogStudentUpdated
{
    public function handle(StudentUpdated $event): void
    {
        Log::info('Student updated', [
            'student_id' => $event->student->id,
            'name' => $event->student->full_name,
            'status' => $event->student->status->value,
        ]);
    }
}
