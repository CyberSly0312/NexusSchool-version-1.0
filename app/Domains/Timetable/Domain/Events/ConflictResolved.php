<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Events;

use App\Domains\Timetable\Infrastructure\Models\TimetableConflict;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConflictResolved
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly TimetableConflict $conflict,
        public readonly ?User $actor = null,
    ) {
    }
}
