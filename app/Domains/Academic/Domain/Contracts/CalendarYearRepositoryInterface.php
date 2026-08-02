<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Contracts;

use App\Domains\Academic\Domain\ValueObjects\DateRange;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;

/**
 * Persistence contract for the calendar-year aggregate invariants.
 */
interface CalendarYearRepositoryInterface
{
    public function activeForSchool(string $schoolId): ?AcademicCalendarYear;

    public function hasActiveForSchool(string $schoolId): bool;

    /**
     * Finds a term of the same year whose range overlaps $range.
     */
    public function overlappingTerm(
        string $schoolId,
        int $calendarYearId,
        DateRange $range,
        ?int $excludeTermId = null,
    ): ?AcademicCalendarTerm;
}
