<?php

declare(strict_types=1);

namespace App\Domains\StudentEnrollment\Events;

use App\Models\StudentEnrollment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentEnrolled
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly StudentEnrollment $enrollment,
    ) {
    }
}
