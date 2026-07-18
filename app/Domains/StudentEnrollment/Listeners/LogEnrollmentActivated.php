<?php

declare(strict_types=1);

namespace App\Domains\StudentEnrollment\Listeners;

use App\Domains\StudentEnrollment\Events\EnrollmentActivated;
use Illuminate\Support\Facades\Log;

class LogEnrollmentActivated
{
    public function handle(EnrollmentActivated $event): void
    {
        Log::info('Enrollment activated', [
            'enrollment_id' => $event->enrollment->id,
            'enrollment_number' => $event->enrollment->enrollment_number,
            'school_id' => $event->enrollment->school_id,
        ]);
    }
}
