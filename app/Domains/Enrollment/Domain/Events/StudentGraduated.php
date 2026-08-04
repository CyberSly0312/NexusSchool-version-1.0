<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Events;

use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentGraduation;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a student graduates from a level. The enrollment is closed
 * (terminal status) and the graduation record is kept in history.
 */
class StudentGraduated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Enrollment $enrollment,
        public readonly EnrollmentGraduation $graduation,
        public readonly ?User $actor = null,
    ) {
    }
}
