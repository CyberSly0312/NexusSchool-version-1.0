<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Events;

use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Domains\Timetable\Infrastructure\Models\TimetableConflict;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConflictDetected
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  list<TimetableConflict>  $conflicts
     */
    public function __construct(
        public readonly Timetable $timetable,
        public readonly array $conflicts,
        public readonly string $detectionId,
        public readonly ?User $actor = null,
    ) {
    }
}
