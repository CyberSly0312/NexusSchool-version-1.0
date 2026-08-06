<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Contracts;

use App\Domains\ClassCouncil\Domain\References\EnrollmentReference;

/**
 * Read-only gateway to the Enrollment engine (SPR-013). Provides the active
 * students a council session reviews.
 */
interface EnrollmentContract
{
    /**
     * Active enrollments of one class (optionally one section) for a year.
     *
     * @return list<EnrollmentReference>
     */
    public function activeEnrollments(
        string $schoolId,
        int|string $yearId,
        int|string $classId,
        int|string|null $sectionId = null,
    ): array;
}
