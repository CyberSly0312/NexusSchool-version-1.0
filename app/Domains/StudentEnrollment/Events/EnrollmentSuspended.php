<?php

namespace App\Domains\StudentEnrollment\Events;

use App\Models\StudentEnrollment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnrollmentSuspended
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly StudentEnrollment $enrollment,
    ) {}
}
