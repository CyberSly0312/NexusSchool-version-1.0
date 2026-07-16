<?php

namespace App\Domains\StudentEnrollment\Listeners;

use App\Domains\StudentEnrollment\Events\EnrollmentSuspended;
use Illuminate\Support\Facades\Log;

class LogEnrollmentSuspended
{
    public function handle(EnrollmentSuspended $event): void
    {
        Log::info('Enrollment suspended', [
            'enrollment_id' => $event->enrollment->id,
            'enrollment_number' => $event->enrollment->enrollment_number,
            'school_id' => $event->enrollment->school_id,
        ]);
    }
}
