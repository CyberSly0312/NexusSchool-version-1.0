<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\Actions;

use App\Domains\Academic\Domain\Enums\CalendarYearStatus;
use App\Domains\Academic\Domain\Events\CalendarYearArchived;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class ArchiveCalendarYearAction
{
    public function execute(AcademicCalendarYear $calendarYear, ?User $actor = null): AcademicCalendarYear
    {
        return DB::transaction(function () use ($calendarYear, $actor): AcademicCalendarYear {
            $calendarYear->update(['status' => CalendarYearStatus::Archived]);

            CalendarYearArchived::dispatch($calendarYear->fresh(), $actor);

            return $calendarYear->refresh();
        });
    }
}
