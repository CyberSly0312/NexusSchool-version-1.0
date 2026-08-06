<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Contracts;

use App\Domains\Timetable\Domain\References\StudentReference;

/**
 * Read-only gateway to the Enrollment domain. Used to answer "who is in which
 * class" questions when the generator needs class cohorts or when section-level
 * timetables must resolve to students.
 */
interface EnrollmentContract
{
    /**
     * Students actively enrolled in a class.
     *
     * @return list<StudentReference>
     */
    public function studentsOfClass(string $schoolId, int|string $classId): array;

    public function isStudentEnrolled(string $schoolId, int|string $studentId, int|string $classId): bool;
}
