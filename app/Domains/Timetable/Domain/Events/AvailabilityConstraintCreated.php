<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Events;

use App\Domains\Timetable\Infrastructure\Models\AvailabilityConstraint;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AvailabilityConstraintCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AvailabilityConstraint $constraint,
        public readonly Timetable $timetable,
        public readonly ?User $actor = null,
    ) {
    }
}
