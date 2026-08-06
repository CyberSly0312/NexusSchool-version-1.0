<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Contracts;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Timetable\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Timetable\Domain\References\ClassReference;
use App\Domains\Timetable\Domain\References\SectionReference;
use App\Domains\Timetable\Domain\References\SubjectReference;
use App\Domains\Timetable\Domain\References\TermReference;
use App\Domains\Timetable\Domain\References\YearReference;

/**
 * Maps the Academic Framework models onto the AcademicFrameworkContract. This is
 * the only place in the Timetable domain allowed to see Academic models.
 */
final class AcademicFrameworkAdapter implements AcademicFrameworkContract
{
    public function years(string $schoolId): array
    {
        return AcademicCalendarYear::query()
            ->forSchool($schoolId)
            ->orderBy('start_date')
            ->get()
            ->map(fn (AcademicCalendarYear $year): YearReference => $this->toYear($year))
            ->all();
    }

    public function year(string $schoolId, int|string $yearId): ?YearReference
    {
        $year = AcademicCalendarYear::query()->forSchool($schoolId)->find($yearId);

        return $year !== null ? $this->toYear($year) : null;
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

    public function term(string $schoolId, int|string $termId): ?TermReference
    {
        $term = AcademicCalendarTerm::query()->forSchool($schoolId)->find($termId);

        return $term !== null ? $this->toTerm($term) : null;
    }

    public function classes(string $schoolId, int|string|null $yearId = null): array
    {
        return SchoolClass::query()
            ->forSchool($schoolId)
            ->when($yearId !== null, function ($query) use ($yearId) {
                $query->whereHas('program.academicCalendarYear', fn ($q) => $q->whereKey($yearId));
            })
            ->orderBy('name')
            ->get()
            ->map(fn (SchoolClass $class): ClassReference => $this->toClass($class))
            ->all();
    }

    public function class(string $schoolId, int|string $classId): ?ClassReference
    {
        $class = SchoolClass::query()->forSchool($schoolId)->find($classId);

        return $class !== null ? $this->toClass($class) : null;
    }

    public function sections(string $schoolId, int|string|null $classId = null): array
    {
        return ClassSection::query()
            ->forSchool($schoolId)
            ->when($classId !== null, fn ($query) => $query->where('school_class_id', $classId))
            ->orderBy('name')
            ->get()
            ->map(fn (ClassSection $section): SectionReference => $this->toSection($section))
            ->all();
    }

    public function section(string $schoolId, int|string $sectionId): ?SectionReference
    {
        $section = ClassSection::query()->forSchool($schoolId)->find($sectionId);

        return $section !== null ? $this->toSection($section) : null;
    }

    public function subjects(string $schoolId): array
    {
        return AcademicSubject::query()
            ->forSchool($schoolId)
            ->orderBy('name')
            ->get()
            ->map(fn (AcademicSubject $subject): SubjectReference => $this->toSubject($subject))
            ->all();
    }

    public function subject(string $schoolId, int|string $subjectId): ?SubjectReference
    {
        $subject = AcademicSubject::query()->forSchool($schoolId)->find($subjectId);

        return $subject !== null ? $this->toSubject($subject) : null;
    }

    public function classSubjects(string $schoolId, int|string $classId): array
    {
        $class = SchoolClass::query()->forSchool($schoolId)->with('coefficients.subject')->find($classId);

        if ($class === null) {
            return [];
        }

        return $class->coefficients
            ->filter(fn ($coefficient) => $coefficient->subject !== null)
            ->sortBy('coefficient')
            ->map(fn ($coefficient): SubjectReference => $this->toSubject($coefficient->subject))
            ->values()
            ->all();
    }

    private function toYear(AcademicCalendarYear $year): YearReference
    {
        return new YearReference(
            id: $year->getKey(),
            name: (string) $year->name,
            code: $year->code,
            startDate: $year->start_date->toDateString(),
            endDate: $year->end_date->toDateString(),
            isActive: $year->status->value === 'active',
        );
    }

    private function toTerm(AcademicCalendarTerm $term): TermReference
    {
        return new TermReference(
            id: $term->getKey(),
            name: (string) $term->name,
            code: $term->code,
            startsOn: $term->starts_on->toDateString(),
            endsOn: $term->ends_on->toDateString(),
            isOpen: $term->isOpen(),
        );
    }

    private function toClass(SchoolClass $class): ClassReference
    {
        return new ClassReference(
            id: $class->getKey(),
            name: (string) $class->name,
            code: $class->code,
        );
    }

    private function toSection(ClassSection $section): SectionReference
    {
        return new SectionReference(
            id: $section->getKey(),
            name: (string) $section->name,
            code: $section->code,
            classId: $section->school_class_id,
        );
    }

    private function toSubject(AcademicSubject $subject): SubjectReference
    {
        return new SubjectReference(
            id: $subject->getKey(),
            name: (string) $subject->name,
            code: $subject->code,
            abbreviation: $subject->abbreviation,
        );
    }
}
