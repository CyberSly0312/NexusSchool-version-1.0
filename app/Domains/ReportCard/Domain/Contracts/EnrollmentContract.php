<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Contracts;

use App\Domains\ReportCard\Domain\References\EnrollmentReference;

/**
 * Read-only gateway to the Enrollment engine (SPR-011). Provides the active
 * students a report card run must cover.
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
