<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Contracts;

use App\Domains\Attendance\Domain\References\TeacherReference;

/**
 * Read-only gateway to the Staff domain (SPR-008). Used to resolve the teacher
 * attached to a session.
 */
interface StaffContract
{
    public function teacher(string $schoolId, int|string $staffId): ?TeacherReference;

    public function isTeacher(string $schoolId, int|string $staffId): bool;

    /**
     * @return list<int|string>
     */
    public function teacherSubjects(string $schoolId, int|string $staffId): array;
}
