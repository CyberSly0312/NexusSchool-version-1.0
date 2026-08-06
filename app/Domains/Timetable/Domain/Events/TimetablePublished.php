<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Events;

use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimetablePublished
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Timetable $timetable,
        public readonly int $version,
        public readonly ?User $actor = null,
    ) {
    }
}
