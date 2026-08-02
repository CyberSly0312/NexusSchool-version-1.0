<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Events;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CalendarTermCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AcademicCalendarTerm $term,
        public readonly ?User $actor = null,
    ) {
    }
}
