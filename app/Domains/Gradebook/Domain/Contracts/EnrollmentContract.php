<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Contracts;

use App\Domains\Gradebook\Domain\References\EnrollmentReference;
use App\Domains\Gradebook\Domain\References\StudentReference;

/**
 * Read-only gateway to the Enrollment domain (SPR-010).
 *
 * Gradebook entries are bound to enrollments: a student must hold an active
 * enrollment in the gradebook's class to receive a term grade. This contract
 * is the only way the engine learns "who is in which class".
 */
interface EnrollmentContract
{
    public function enrollment(string $schoolId, int|string $enrollmentId): ?EnrollmentReference;

    /**
     * Active enrollments of a class, optionally restricted to an academic year.
     *
     * @return list<EnrollmentReference>
     */
    public function enrollmentsOfClass(string $schoolId, int|string $classId, int|string|null $yearId = null, int|string|null $sectionId = null): array;

    /**
     * Students actively enrolled in a class.
     *
     * @return list<StudentReference>
     */
    public function studentsOfClass(string $schoolId, int|string $classId, int|string|null $yearId = null, int|string|null $sectionId = null): array;
}
