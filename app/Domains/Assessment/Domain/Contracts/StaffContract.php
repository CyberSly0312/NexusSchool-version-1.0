<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Contracts;

use App\Domains\Assessment\Domain\References\TeacherReference;

/**
 * Read-only gateway to the Staff domain (SPR-009). Used to resolve the teacher
 * attached to an assessment and to enforce that teachers only grade the
 * subjects they are assigned to.
 */
interface StaffContract
{
    public function teacher(string $schoolId, int|string $staffId): ?TeacherReference;

    /**
     * The teacher profile linked to a platform user (via staff.user_id).
     */
    public function teacherForUser(string $schoolId, int|string $userId): ?TeacherReference;

    public function isTeacher(string $schoolId, int|string $staffId): bool;

    /**
     * @return list<int|string>
     */
    public function teacherSubjects(string $schoolId, int|string $staffId): array;
}
