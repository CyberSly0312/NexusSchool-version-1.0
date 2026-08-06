<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Infrastructure\Contracts;

use App\Domains\Academic\Domain\Enums\CalendarYearStatus;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Gradebook\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Gradebook\Domain\References\ClassReference;
use App\Domains\Gradebook\Domain\References\SectionReference;
use App\Domains\Gradebook\Domain\References\SubjectReference;
use App\Domains\Gradebook\Domain\References\TermReference;
use App\Domains\Gradebook\Domain\References\YearReference;

/**
 * Maps the Academic Framework models onto the Gradebook AcademicFrameworkContract.
 * This is the only place in the Gradebook domain allowed to see Academic models.
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

    public function schoolClass(string $schoolId, int|string $classId): ?ClassReference
    {
        $class = SchoolClass::query()->forSchool($schoolId)->with('program')->find($classId);

        return $class !== null ? $this->toClass($class) : null;
    }

    public function section(string $schoolId, int|string $sectionId): ?SectionReference
    {
        $section = ClassSection::query()->forSchool($schoolId)->find($sectionId);

        return $section !== null ? $this->toSection($section) : null;
    }

    private function toYear(AcademicCalendarYear $year): YearReference
    {
        return new YearReference(
            id: $year->getKey(),
            name: (string) $year->name,
            code: $year->code,
            status: $year->status->value,
        );
    }

    private function toTerm(AcademicCalendarTerm $term): TermReference
    {
        return new TermReference(
            id: $term->getKey(),
            name: (string) $term->name,
            code: $term->code,
            startsOn: $term->starts_on?->toDateString(),
            endsOn: $term->ends_on?->toDateString(),
            status: $term->status->value,
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

    private function toClass(SchoolClass $class): ClassReference
    {
        return new ClassReference(
            id: $class->getKey(),
            name: (string) $class->name,
            code: $class->code,
            level: $class->education_level_id !== null ? (string) $class->education_level_id : null,
            program: $class->program?->name,
        );
    }

    private function toSection(ClassSection $section): SectionReference
    {
        return new SectionReference(
            id: $section->getKey(),
            name: (string) $section->name,
            classId: $section->school_class_id,
        );
    }
}
