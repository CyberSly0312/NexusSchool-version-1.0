<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Contracts;

use App\Domains\Attendance\Domain\References\EnrollmentReference;
use App\Domains\Attendance\Domain\References\StudentReference;

/**
 * Read-only gateway to the Enrollment domain (SPR-008).
 *
 * Attendance is bound to enrollments, never to students directly: a student
 * must hold an active enrollment for a class to be marked. This contract is the
 * only way the engine learns "who is in which class".
 */
interface EnrollmentContract
{
    /**
     * Students actively enrolled in a class.
     *
     * @return list<StudentReference>
     */
    public function studentsOfClass(string $schoolId, int|string $classId, int|string|null $yearId = null): array;

    /**
     * Active enrollments of a class.
     *
     * @return list<EnrollmentReference>
     */
    public function enrollmentsOfClass(string $schoolId, int|string $classId, int|string|null $yearId = null): array;

    public function enrollment(string $schoolId, int|string $enrollmentId): ?EnrollmentReference;

    public function isStudentEnrolled(string $schoolId, int|string $studentId, int|string $classId): bool;
}
