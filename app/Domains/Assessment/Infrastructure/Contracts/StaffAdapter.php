<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Contracts;

use App\Domains\Assessment\Domain\Contracts\StaffContract;
use App\Domains\Assessment\Domain\References\TeacherReference;
use App\Domains\Staff\Domain\Enums\StaffStatus;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Infrastructure\Models\TeacherProfile;

/**
 * Maps Staff-domain teachers onto the StaffContract. This is the only place in
 * the Assessment domain allowed to see Staff models.
 */
final class StaffAdapter implements StaffContract
{
    public function teacher(string $schoolId, int|string $staffId): ?TeacherReference
    {
        $staff = Staff::query()
            ->forSchool($schoolId)
            ->where('status', StaffStatus::Active)
            ->whereHas('teacherProfile')
            ->with('teacherProfile')
            ->find($staffId);

        return $staff !== null ? $this->toTeacher($staff) : null;
    }

    public function teacherForUser(string $schoolId, int|string $userId): ?TeacherReference
    {
        $staff = Staff::query()
            ->forSchool($schoolId)
            ->where('user_id', $userId)
            ->where('status', StaffStatus::Active)
            ->whereHas('teacherProfile')
            ->with('teacherProfile')
            ->first();

        return $staff !== null ? $this->toTeacher($staff) : null;
    }

    public function isTeacher(string $schoolId, int|string $staffId): bool
    {
        return Staff::query()
            ->forSchool($schoolId)
            ->where('status', StaffStatus::Active)
            ->whereHas('teacherProfile')
            ->whereKey($staffId)
            ->exists();
    }

    public function teacherSubjects(string $schoolId, int|string $staffId): array
    {
        $profile = TeacherProfile::query()
            ->forSchool($schoolId)
            ->where('staff_id', $staffId)
            ->first();

        if ($profile === null) {
            return [];
        }

        return array_values(array_map(
            'intval',
            array_filter((array) $profile->teaching_subject_ids),
        ));
    }

    private function toTeacher(Staff $staff): TeacherReference
    {
        return new TeacherReference(
            id: $staff->getKey(),
            fullName: (string) $staff->full_name,
            firstName: (string) $staff->first_name,
            lastName: (string) $staff->last_name,
            subjectIds: $this->teacherSubjects($staff->school_id, $staff->getKey()),
            isActive: $staff->status === StaffStatus::Active,
        );
    }
}
