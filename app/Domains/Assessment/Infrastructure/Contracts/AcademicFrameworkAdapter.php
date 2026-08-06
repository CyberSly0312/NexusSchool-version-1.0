<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Contracts;

use App\Domains\Academic\Domain\Enums\CalendarYearStatus;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Assessment\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Assessment\Domain\References\SubjectReference;
use App\Domains\Assessment\Domain\References\TermReference;
use App\Domains\Assessment\Domain\References\YearReference;

/**
 * Maps the Academic Framework models onto the Assessment AcademicFrameworkContract.
 * This is the only place in the Assessment domain allowed to see Academic models.
 */
final class AcademicFrameworkAdapter implements AcademicFrameworkContract
{
    public function year(string $schoolId, int|string $yearId): ?YearReference
    {
        $year = AcademicCalendarYear::query()->forSchool($schoolId)->find($yearId);

        return $year !== null ? $this->toYear($year) : null;
    }

    public function activeYear(string $schoolId): ?YearReference
    {
        $year = AcademicCalendarYear::query()
            ->forSchool($schoolId)
            ->where('status', CalendarYearStatus::Active)
            ->orderByDesc('start_date')
            ->first();

        return $year !== null ? $this->toYear($year) : null;
    }

    public function term(string $schoolId, int|string $termId): ?TermReference
    {
        $term = AcademicCalendarTerm::query()->forSchool($schoolId)->find($termId);

        return $term !== null ? $this->toTerm($term) : null;
    }

    public function terms(string $schoolId, int|string $yearId): array
    {
        return AcademicCalendarTerm::query()
            ->forSchool($schoolId)
            ->where('calendar_year_id', $yearId)
            ->orderBy('starts_on')
            ->get()
            ->map(fn (AcademicCalendarTerm $term): TermReference => $this->toTerm($term))
            ->all();
    }

    public function subject(string $schoolId, int|string $subjectId): ?SubjectReference
    {
        $subject = AcademicSubject::query()->forSchool($schoolId)->find($subjectId);

        return $subject !== null ? $this->toSubject($subject) : null;
    }

    private function toYear(AcademicCalendarYear $year): YearReference
    {
        return new YearReference(
            id: $year->getKey(),
            name: (string) $year->name,
            code: $year->code,
            startDate: $year->start_date?->toDateString() ?? '',
            endDate: $year->end_date?->toDateString() ?? '',
            isActive: $year->status->value === 'active',
        );
    }

    private function toTerm(AcademicCalendarTerm $term): TermReference
    {
        return new TermReference(
            id: $term->getKey(),
            name: (string) $term->name,
            code: $term->code,
            startsOn: $term->starts_on?->toDateString() ?? '',
            endsOn: $term->ends_on?->toDateString() ?? '',
            isOpen: $term->isOpen(),
        );
    }

    private function toSubject(AcademicSubject $subject): SubjectReference
    {
        return new SubjectReference(
            id: $subject->getKey(),
            name: (string) $subject->name,
            code: $subject->code,
        );
    }
}
