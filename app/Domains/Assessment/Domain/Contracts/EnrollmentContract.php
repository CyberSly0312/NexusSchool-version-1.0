<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Contracts;

use App\Domains\Assessment\Domain\References\EnrollmentReference;
use App\Domains\Assessment\Domain\References\StudentReference;

/**
 * Read-only gateway to the Enrollment domain (SPR-009).
 *
 * Scores are bound to enrollments, never to students directly: a student must
 * hold an active enrollment in the assessment's class to be graded. This
 * contract is the only way the engine learns "who is in which class".
 */
interface EnrollmentContract
{
    public function enrollment(string $schoolId, int|string $enrollmentId): ?EnrollmentReference;

    /**
     * Active enrollments of a class, optionally restricted to an academic year.
     *
     * @return list<EnrollmentReference>
     */
    public function enrollmentsOfClass(string $schoolId, int|string $classId, int|string|null $yearId = null): array;

    /**
     * Students actively enrolled in a class.
     *
     * @return list<StudentReference>
     */
    public function studentsOfClass(string $schoolId, int|string $classId, int|string|null $yearId = null): array;

    public function isStudentEnrolled(string $schoolId, int|string $studentId, int|string $classId): bool;
}
