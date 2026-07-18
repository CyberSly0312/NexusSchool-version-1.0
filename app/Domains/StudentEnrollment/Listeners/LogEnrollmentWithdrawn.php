<?php

declare(strict_types=1);

namespace App\Domains\StudentEnrollment\Listeners;

use App\Domains\StudentEnrollment\Events\EnrollmentWithdrawn;
use Illuminate\Support\Facades\Log;

class LogEnrollmentWithdrawn
{
    public function handle(EnrollmentWithdrawn $event): void
    {
        Log::info('Enrollment withdrawn', [
            'enrollment_id' => $event->enrollment->id,
            'enrollment_number' => $event->enrollment->enrollment_number,
            'school_id' => $event->enrollment->school_id,
        ]);
    }
}
