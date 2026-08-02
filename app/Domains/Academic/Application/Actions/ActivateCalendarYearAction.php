<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\Actions;

use App\Domains\Academic\Domain\Enums\CalendarYearStatus;
use App\Domains\Academic\Domain\Events\CalendarYearActivated;
use App\Domains\Academic\Domain\Exceptions\ActiveCalendarYearAlreadyExistsException;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class ActivateCalendarYearAction
{
    public function execute(AcademicCalendarYear $calendarYear, ?User $actor = null): AcademicCalendarYear
    {
        if ($calendarYear->isActive()) {
            throw ActiveCalendarYearAlreadyExistsException::forSchool(
                $calendarYear->school_id,
                $calendarYear->name,
            );
        }

        return DB::transaction(function () use ($calendarYear, $actor): AcademicCalendarYear {
            // The invariant "exactly one active year per school" is enforced by a
            // partial unique index on sqlite/pgsql/mariadb and here by construction:
            // demote the current active year before promoting the new one.
            AcademicCalendarYear::query()
                ->forSchool($calendarYear->school_id)
                ->whereKeyNot($calendarYear->id)
                ->active()
                ->update(['status' => CalendarYearStatus::Draft]);

            $calendarYear->update(['status' => CalendarYearStatus::Active]);

            CalendarYearActivated::dispatch($calendarYear->fresh(), $actor);

            return $calendarYear->refresh();
        });
    }
}
