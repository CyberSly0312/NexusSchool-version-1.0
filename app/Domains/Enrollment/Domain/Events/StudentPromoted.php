<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Events;

use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentPromotion;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a student is promoted into a new enrollment row. The
 * previous enrollment row is archived and stays in history.
 */
class StudentPromoted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Enrollment $enrollment,
        public readonly EnrollmentPromotion $promotion,
        public readonly ?User $actor = null,
    ) {
    }
}
