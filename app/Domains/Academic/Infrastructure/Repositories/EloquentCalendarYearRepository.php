<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Repositories;

use App\Domains\Academic\Domain\Contracts\CalendarYearRepositoryInterface;
use App\Domains\Academic\Domain\Enums\CalendarYearStatus;
use App\Domains\Academic\Domain\ValueObjects\DateRange;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;

final class EloquentCalendarYearRepository implements CalendarYearRepositoryInterface
{
    public function activeForSchool(string $schoolId): ?AcademicCalendarYear
    {
        return AcademicCalendarYear::query()
            ->forSchool($schoolId)
            ->active()
            ->first();
    }

    public function hasActiveForSchool(string $schoolId): bool
    {
        return $this->activeForSchool($schoolId) !== null;
    }

    public function overlappingTerm(
        string $schoolId,
        int $calendarYearId,
        DateRange $range,
        ?int $excludeTermId = null,
    ): ?AcademicCalendarTerm {
        return AcademicCalendarTerm::query()
            ->forSchool($schoolId)
            ->where('calendar_year_id', $calendarYearId)
            ->where('starts_on', '<', $range->endsOn->format('Y-m-d'))
            ->where('ends_on', '>', $range->startsOn->format('Y-m-d'))
            ->when($excludeTermId !== null, fn ($query) => $query->whereKeyNot($excludeTermId))
            ->first();
    }

    /**
     * @return list<AcademicCalendarYear>
     */
    public static function allForSchool(string $schoolId): array
    {
        return AcademicCalendarYear::query()
            ->forSchool($schoolId)
            ->orderByDesc('start_date')
            ->get()
            ->all();
    }

    public static function switchActive(string $schoolId, int $yearId): void
    {
        AcademicCalendarYear::query()
            ->forSchool($schoolId)
            ->whereKeyNot($yearId)
            ->active()
            ->update(['status' => CalendarYearStatus::Draft]);
    }
}
