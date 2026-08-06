<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Contracts;

use App\Domains\Timetable\Domain\References\TeacherReference;

/**
 * Read-only gateway to teachers (a Staff specialization). The Timetable engine
 * never talks to Staff models directly — only to the projections below.
 * Implementations live in the Infrastructure layer of this domain.
 */
interface StaffContract
{
    /**
     * @return list<TeacherReference>
     */
    public function teachers(string $schoolId): array;

    public function teacher(string $schoolId, int|string $staffId): ?TeacherReference;

    public function isTeacher(string $schoolId, int|string $staffId): bool;

    /**
     * Subjects a teacher may teach.
     *
     * @return list<int|string>
     */
    public function teacherSubjects(string $schoolId, int|string $staffId): array;
}
