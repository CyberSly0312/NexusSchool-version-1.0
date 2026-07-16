<?php

namespace App\Domains\StudentEnrollment\Listeners;

use App\Domains\StudentEnrollment\Events\EnrollmentCompleted;
use Illuminate\Support\Facades\Log;

class LogEnrollmentCompleted
{
    public function handle(EnrollmentCompleted $event): void
    {
        Log::info('Enrollment completed', [
            'enrollment_id' => $event->enrollment->id,
            'enrollment_number' => $event->enrollment->enrollment_number,
            'school_id' => $event->enrollment->school_id,
        ]);
    }
}
