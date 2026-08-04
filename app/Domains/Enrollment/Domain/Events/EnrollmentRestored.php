<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Events;

use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a suspended enrollment is restored to active.
 */
class EnrollmentRestored
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Enrollment $enrollment,
        public readonly ?User $actor = null,
    ) {
    }
}
