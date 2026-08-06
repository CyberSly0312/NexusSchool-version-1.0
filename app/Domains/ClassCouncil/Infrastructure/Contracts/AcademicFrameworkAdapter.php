<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Infrastructure\Contracts;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\ClassCouncil\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\ClassCouncil\Domain\References\AcademicPeriodReference;

/**
 * AcademicFrameworkContract implementation (SPR-013). Projects the Academic
 * models onto the reference so the Class Council engine never sees them.
 */
final class AcademicFrameworkAdapter implements AcademicFrameworkContract
{
    public function period(
        string $schoolId,
        int|string $yearId,
        int|string $termId,
        int|string|null $classId = null,
        int|string|null $sectionId = null,
    ): ?AcademicPeriodReference {
        $year = AcademicCalendarYear::query()->forSchool($schoolId)->find($yearId);
        $term = AcademicCalendarTerm::query()->forSchool($schoolId)->find($termId);

        if ($year === null || $term === null) {
            return null;
        }

        $class = $classId !== null ? SchoolClass::query()->forSchool($schoolId)->find($classId) : null;
        $section = $sectionId !== null ? ClassSection::query()->forSchool($schoolId)->find($sectionId) : null;

        return new AcademicPeriodReference(
            classId: $class?->getKey() ?? $classId,
            className: $class?->name,
            sectionId: $section?->getKey() ?? $sectionId,
            sectionName: $section?->name,
            termId: $term->getKey(),
            termName: $term->name,
            yearId: $year->getKey(),
            yearName: $year->name,
        );
    }
}
