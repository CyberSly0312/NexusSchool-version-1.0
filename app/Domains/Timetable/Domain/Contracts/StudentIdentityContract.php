<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Contracts;

use App\Domains\Timetable\Domain\References\StudentReference;

/**
 * Read-only gateway to the Student identity domain. The Timetable engine never
 * talks to Student models directly.
 */
interface StudentIdentityContract
{
    public function student(string $schoolId, int|string $studentId): ?StudentReference;

    /**
     * @return list<StudentReference>
     */
    public function students(string $schoolId): array;
}
