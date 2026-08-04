<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\Services;

use App\Domains\Academic\Infrastructure\Models\AcademicProgram;
use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Enrollment\Domain\Exceptions\InactiveAcademicYearException;
use App\Domains\Enrollment\Domain\Rules\ActiveAcademicYearRule;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use InvalidArgumentException;

/**
 * Centralizes the academic-structure lookups shared by the enrollment actions:
 * active-year checks and school/class/section/program/level ownership guards.
 * Every action enforces the same rules through this single resolver so a
 * duplicated guard cannot drift out of sync.
 */
final class EnrollmentAcademicResolver
{
    public function assertActiveAcademicYear(int $academicCalendarYearId): void
    {
        if ((new ActiveAcademicYearRule($academicCalendarYearId))->fails()) {
            throw InactiveAcademicYearException::message(
                "The academic year #{$academicCalendarYearId} does not exist or is not active.",
            );
        }
    }

    public function assertClassInSchool(string $schoolId, int $classId): SchoolClass
    {
        $class = SchoolClass::query()->forSchool($schoolId)->find($classId);

        if ($class === null) {
            throw new InvalidArgumentException(
                "The class #{$classId} does not belong to school {$schoolId}.",
            );
        }

        return $class;
    }

    public function assertSectionBelongsToClass(SchoolClass $class, ?int $sectionId): void
    {
        if ($sectionId === null) {
            return;
        }

        if (! $class->sections()->whereKey($sectionId)->exists()) {
            throw new InvalidArgumentException(
                "The class section #{$sectionId} does not belong to class #{$class->getKey()}.",
            );
        }
    }

    public function assertProgramInSchool(string $schoolId, int $programId): void
    {
        $program = AcademicProgram::query()->forSchool($schoolId)->find($programId);

        if ($program === null) {
            throw new InvalidArgumentException(
                "The program #{$programId} does not belong to school {$schoolId}.",
            );
        }
    }

    public function assertEducationLevelInSchool(string $schoolId, int $levelId): void
    {
        $level = EducationLevel::query()->forSchool($schoolId)->find($levelId);

        if ($level === null) {
            throw new InvalidArgumentException(
                "The education level #{$levelId} does not belong to school {$schoolId}.",
            );
        }
    }

    public function assertSectionBelongsToEnrollmentClass(Enrollment $enrollment, ?int $sectionId): void
    {
        if ($sectionId === null) {
            return;
        }

        $class = $enrollment->schoolClass()->first();

        if ($class === null || ! $class->sections()->whereKey($sectionId)->exists()) {
            throw new InvalidArgumentException(
                "The class section #{$sectionId} does not belong to the enrollment class.",
            );
        }
    }
}
