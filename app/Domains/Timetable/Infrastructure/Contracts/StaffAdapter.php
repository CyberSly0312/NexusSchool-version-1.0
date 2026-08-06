<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Contracts;

use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Infrastructure\Models\TeacherProfile;
use App\Domains\Timetable\Domain\Contracts\StaffContract;
use App\Domains\Timetable\Domain\References\TeacherReference;

/**
 * Maps Staff-domain teachers onto the StaffContract. This is the only place in
 * the Timetable domain allowed to see Staff models.
 */
final class StaffAdapter implements StaffContract
{
    public function teachers(string $schoolId): array
    {
        return Staff::query()
            ->forSchool($schoolId)
            ->where('status', 'active')
            ->whereHas('teacherProfile')
            ->with('teacherProfile')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(fn (Staff $staff): TeacherReference => $this->toTeacher($staff))
            ->all();
    }

    public function teacher(string $schoolId, int|string $staffId): ?TeacherReference
    {
        $staff = Staff::query()
            ->forSchool($schoolId)
            ->where('status', 'active')
            ->whereHas('teacherProfile')
            ->with('teacherProfile')
            ->find($staffId);

        return $staff !== null ? $this->toTeacher($staff) : null;
    }

    public function isTeacher(string $schoolId, int|string $staffId): bool
    {
        return Staff::query()
            ->forSchool($schoolId)
            ->where('status', 'active')
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
        $profile = $staff->teacherProfile;

        return new TeacherReference(
            id: $staff->getKey(),
            fullName: (string) $staff->full_name,
            firstName: (string) $staff->first_name,
            lastName: (string) $staff->last_name,
            subjectIds: array_values(array_map(
                'intval',
                array_filter((array) ($profile?->teaching_subject_ids ?? [])),
            )),
            maxWeeklyHours: $profile?->max_weekly_hours,
            isActive: $staff->status === 'active',
        );
    }
}
