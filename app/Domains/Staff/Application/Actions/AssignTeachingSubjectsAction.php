<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\Actions;

use App\Domains\Staff\Domain\Enums\StaffSpecialization;
use App\Domains\Staff\Domain\Events\TeacherSubjectsAssigned;
use App\Domains\Staff\Domain\Exceptions\InvalidStaffStateException;
use App\Domains\Staff\Domain\Exceptions\TeacherSpecializationException;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Infrastructure\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Assigns the teaching subjects and preferred levels of a Teacher. Guarded by
 * the Teacher specialization: non-teacher staff can never hold a
 * TeacherProfile.
 */
final class AssignTeachingSubjectsAction
{
    /**
     * @param  array<int>  $teachingSubjectIds
     * @param  array<int|string>  $preferredTeachingLevels
     */
    public function execute(
        Staff $staff,
        array $teachingSubjectIds = [],
        array $preferredTeachingLevels = [],
        ?User $actor = null,
    ): TeacherProfile {
        $this->assertTeacher($staff);
        $this->assertMutable($staff);

        return DB::transaction(function () use ($staff, $teachingSubjectIds, $preferredTeachingLevels, $actor): TeacherProfile {
            $profile = TeacherProfile::query()
                ->forSchool($staff->school_id)
                ->where('staff_id', $staff->getKey())
                ->first();

            if ($profile === null) {
                $profile = TeacherProfile::create([
                    'school_id' => $staff->school_id,
                    'staff_id' => $staff->getKey(),
                    'teaching_subject_ids' => $teachingSubjectIds,
                    'preferred_teaching_levels' => $preferredTeachingLevels,
                ]);
            } else {
                $profile->update([
                    'teaching_subject_ids' => $teachingSubjectIds,
                    'preferred_teaching_levels' => $preferredTeachingLevels,
                ]);
            }

            TeacherSubjectsAssigned::dispatch($profile, $actor);

            return $profile;
        });
    }

    private function assertTeacher(Staff $staff): void
    {
        if ($staff->specialization !== StaffSpecialization::Teacher) {
            throw TeacherSpecializationException::notTeacher($staff->getKey(), $staff->specialization);
        }
    }

    private function assertMutable(Staff $staff): void
    {
        if ($staff->isImmutable()) {
            throw InvalidStaffStateException::immutable($staff->getKey());
        }
    }
}
