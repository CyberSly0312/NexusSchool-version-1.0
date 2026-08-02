<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Events;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CalendarYearCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AcademicCalendarYear $calendarYear,
        public readonly ?User $actor = null,
    ) {
    }
}
