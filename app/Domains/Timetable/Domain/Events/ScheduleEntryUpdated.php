<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Events;

use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScheduleEntryUpdated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $changes
     */
    public function __construct(
        public readonly ScheduleEntry $entry,
        public readonly Timetable $timetable,
        public readonly array $changes = [],
        public readonly ?User $actor = null,
    ) {
    }
}
