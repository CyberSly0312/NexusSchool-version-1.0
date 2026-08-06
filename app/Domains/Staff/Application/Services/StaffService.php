<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\Services;

use App\Domains\Staff\Application\Actions\AssignDepartmentAction;
use App\Domains\Staff\Application\Actions\AssignPositionAction;
use App\Domains\Staff\Application\Actions\AssignTeachingSubjectsAction;
use App\Domains\Staff\Application\Actions\CreateStaffAction;
use App\Domains\Staff\Application\Actions\RestoreStaffAction;
use App\Domains\Staff\Application\Actions\StartEmploymentAction;
use App\Domains\Staff\Application\Actions\SuspendStaffAction;
use App\Domains\Staff\Application\Actions\TerminateEmploymentAction;
use App\Domains\Staff\Application\Actions\TransferStaffAction;
use App\Domains\Staff\Application\Actions\UpdateStaffAction;
use App\Domains\Staff\Infrastructure\Models\Assignment;
use App\Domains\Staff\Infrastructure\Models\Employment;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Infrastructure\Models\TeacherProfile;
use App\Models\User;

/**
 * Application-service facade over the Staff & Workforce Engine actions
 * (SPR-006). Presentation and cross-domain consumers depend on this class
 * only, never on the underlying actions directly.
 */
final class StaffService
{
    public function __construct(
        private readonly CreateStaffAction $createStaffAction,
        private readonly UpdateStaffAction $updateStaffAction,
        private readonly StartEmploymentAction $startEmploymentAction,
        private readonly AssignDepartmentAction $assignDepartmentAction,
        private readonly AssignPositionAction $assignPositionAction,
        private readonly TransferStaffAction $transferStaffAction,
        private readonly TerminateEmploymentAction $terminateEmploymentAction,
        private readonly SuspendStaffAction $suspendStaffAction,
        private readonly RestoreStaffAction $restoreStaffAction,
        private readonly AssignTeachingSubjectsAction $assignTeachingSubjectsAction,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(string $schoolId, array $attributes, ?User $actor = null): Staff
    {
        $data = \App\Domains\Staff\Application\DTOs\CreateStaffData::fromArray($attributes + ['school_id' => $schoolId]);

        return $this->createStaffAction->execute($data, $actor);
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    public function update(Staff $staff, array $changes, ?User $actor = null): Staff
    {
        return $this->updateStaffAction->execute($staff, $changes, $actor);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function startEmployment(string $schoolId, array $attributes, ?User $actor = null): Employment
    {
        $data = \App\Domains\Staff\Application\DTOs\EmploymentData::fromArray($attributes + ['school_id' => $schoolId]);

        return $this->startEmploymentAction->execute($data, $actor);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function assignDepartment(string $schoolId, array $attributes, ?User $actor = null): Assignment
    {
        $data = \App\Domains\Staff\Application\DTOs\AssignmentData::fromArray($attributes + ['school_id' => $schoolId]);

        return $this->assignDepartmentAction->execute($data, $actor);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function assignPosition(string $schoolId, array $attributes, ?User $actor = null): Assignment
    {
        $data = \App\Domains\Staff\Application\DTOs\AssignmentData::fromArray($attributes + ['school_id' => $schoolId]);

        return $this->assignPositionAction->execute($data, $actor);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function transfer(string $schoolId, array $attributes, ?User $actor = null): Employment
    {
        $data = \App\Domains\Staff\Application\DTOs\TransferData::fromArray($attributes + ['from_school_id' => $schoolId]);

        return $this->transferStaffAction->execute($data, $actor);
    }

    public function terminateEmployment(
        Employment $employment,
        ?string $reason = null,
        ?string $terminationDate = null,
        ?User $actor = null,
    ): Employment {
        return $this->terminateEmploymentAction->execute($employment, $reason, $terminationDate, $actor);
    }

    public function suspend(Staff $staff, ?string $reason = null, ?User $actor = null): Staff
    {
        return $this->suspendStaffAction->execute($staff, $reason, $actor);
    }

    public function restore(Staff $staff, ?User $actor = null): Staff
    {
        return $this->restoreStaffAction->execute($staff, $actor);
    }

    /**
     * @param  array<int>  $teachingSubjectIds
     * @param  array<int|string>  $preferredTeachingLevels
     */
    public function assignTeachingSubjects(
        Staff $staff,
        array $teachingSubjectIds = [],
        array $preferredTeachingLevels = [],
        ?User $actor = null,
    ): TeacherProfile {
        return $this->assignTeachingSubjectsAction->execute($staff, $teachingSubjectIds, $preferredTeachingLevels, $actor);
    }
}
