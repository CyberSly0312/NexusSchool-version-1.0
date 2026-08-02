<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\Services;

use App\Domains\Academic\Application\Actions\ActivateCalendarYearAction;
use App\Domains\Academic\Application\Actions\ArchiveCalendarYearAction;
use App\Domains\Academic\Application\Actions\CreateCalendarTermAction;
use App\Domains\Academic\Application\Actions\CreateCalendarYearAction;
use App\Domains\Academic\Application\DTOs\CalendarEventData;
use App\Domains\Academic\Application\DTOs\CalendarTermData;
use App\Domains\Academic\Application\DTOs\CalendarYearData;
use App\Domains\Academic\Domain\Contracts\CalendarYearRepositoryInterface;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarEvent;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CalendarService
{
    public function __construct(
        private readonly CalendarYearRepositoryInterface $calendarYears,
        private readonly CreateCalendarYearAction $createCalendarYear,
        private readonly ActivateCalendarYearAction $activateCalendarYear,
        private readonly ArchiveCalendarYearAction $archiveCalendarYear,
        private readonly CreateCalendarTermAction $createCalendarTerm,
    ) {
    }

    public function createYear(CalendarYearData $data, ?User $actor = null): AcademicCalendarYear
    {
        return $this->createCalendarYear->execute($data, $actor);
    }

    public function activateYear(AcademicCalendarYear $calendarYear, ?User $actor = null): AcademicCalendarYear
    {
        return $this->activateCalendarYear->execute($calendarYear, $actor);
    }

    public function archiveYear(AcademicCalendarYear $calendarYear, ?User $actor = null): AcademicCalendarYear
    {
        return $this->archiveCalendarYear->execute($calendarYear, $actor);
    }

    public function createTerm(CalendarTermData $data, ?User $actor = null): AcademicCalendarTerm
    {
        return $this->createCalendarTerm->execute($data, $actor);
    }

    public function createEvent(CalendarEventData $data): AcademicCalendarEvent
    {
        return AcademicCalendarEvent::create($data->toArray());
    }

    public function getActiveYearForSchool(string $schoolId): ?AcademicCalendarYear
    {
        return $this->calendarYears->activeForSchool($schoolId);
    }

    public function hasActiveYearForSchool(string $schoolId): bool
    {
        return $this->calendarYears->hasActiveForSchool($schoolId);
    }

    public function switchActiveYear(string $schoolId, int $calendarYearId): AcademicCalendarYear
    {
        return DB::transaction(function () use ($schoolId, $calendarYearId): AcademicCalendarYear {
            $calendarYear = AcademicCalendarYear::query()
                ->forSchool($schoolId)
                ->findOrFail($calendarYearId);

            return $this->activateYear($calendarYear);
        });
    }
}
