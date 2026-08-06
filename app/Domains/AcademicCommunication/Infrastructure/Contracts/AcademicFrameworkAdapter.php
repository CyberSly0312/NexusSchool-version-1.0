<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Contracts;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\AcademicCommunication\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\AcademicCommunication\Domain\References\AcademicYearReference;
use App\Domains\AcademicCommunication\Domain\References\ClassReference;
use App\Domains\AcademicCommunication\Domain\References\SectionReference;
use App\Domains\AcademicCommunication\Domain\References\TermReference;

/**
 * Maps the Academic Framework models onto the engine's AcademicFrameworkContract
 * (SPR-012). This is the only place in the Academic Communication domain allowed
 * to see Academic-domain models.
 */
final class AcademicFrameworkAdapter implements AcademicFrameworkContract
{
    public function year(string $schoolId, int|string $yearId): ?AcademicYearReference
    {
        $year = AcademicCalendarYear::query()->forSchool($schoolId)->find($yearId);

        return $year !== null ? $this->toYear($year) : null;
    }

    public function term(string $schoolId, int|string $termId): ?TermReference
    {
        $term = AcademicCalendarTerm::query()->forSchool($schoolId)->find($termId);

        return $term !== null ? $this->toTerm($term) : null;
    }

    public function class(string $schoolId, int|string $classId): ?ClassReference
    {
        $class = SchoolClass::query()->forSchool($schoolId)->find($classId);

        return $class !== null ? $this->toClass($class) : null;
    }

    public function section(string $schoolId, int|string $sectionId): ?SectionReference
    {
        $section = ClassSection::query()->forSchool($schoolId)->find($sectionId);

        return $section !== null ? $this->toSection($section) : null;
    }

    private function toYear(AcademicCalendarYear $year): AcademicYearReference
    {
        return new AcademicYearReference(
            id: $year->getKey(),
            name: (string) $year->name,
            startDate: $year->start_date?->toDateString(),
            endDate: $year->end_date?->toDateString(),
            status: (string) $year->status?->value,
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
            status: (string) $term->status?->value,
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
        );
    }
}
