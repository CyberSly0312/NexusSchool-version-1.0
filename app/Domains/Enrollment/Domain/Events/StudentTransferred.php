<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Events;

use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentTransfer;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched after an internal (class/section) or school transfer. Internal
 * transfers keep the same enrollment row active; outgoing school transfers
 * archive it.
 */
class StudentTransferred
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Enrollment $enrollment,
        public readonly EnrollmentTransfer $transfer,
        public readonly ?User $actor = null,
    ) {
    }
}
