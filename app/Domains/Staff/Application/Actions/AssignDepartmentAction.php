<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\Actions;

use App\Domains\Staff\Application\DTOs\AssignmentData;
use App\Domains\Staff\Domain\Enums\AssignmentType;
use App\Domains\Staff\Domain\Events\DepartmentAssigned;
use App\Domains\Staff\Domain\Exceptions\AssignmentException;
use App\Domains\Staff\Domain\Exceptions\InvalidStaffStateException;
use App\Domains\Staff\Infrastructure\Models\Assignment;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Assigns a staff member to a department. The assignment is time-aware and
 * never rewrites the immutable employment history.
 */
final class AssignDepartmentAction
{
    public function execute(AssignmentData $data, ?User $actor = null): Assignment
    {
        $staff = Staff::query()->findOrFail($data->staffId);

        $this->assertMutable($staff);
        $this->assertDepartmentBelongsToSchool($data->departmentId, $data->schoolId);
        $this->assertNoOverlapping($staff, $data->schoolId);

        $attributes = $data->toArray();

        return DB::transaction(function () use ($attributes, $actor): Assignment {
            $assignment = Assignment::create($attributes);

            DepartmentAssigned::dispatch($assignment, $actor);

            return $assignment;
        });
    }

    private function assertMutable(Staff $staff): void
    {
        if ($staff->isImmutable()) {
            throw InvalidStaffStateException::immutable($staff->getKey());
        }
    }

    private function assertDepartmentBelongsToSchool(?int $departmentId, string $schoolId): void
    {
        if ($departmentId === null) {
            throw AssignmentException::message('A department assignment requires a department.');
        }

        $exists = Department::query()
            ->forSchool($schoolId)
            ->whereKey($departmentId)
            ->exists();

        if (! $exists) {
            throw AssignmentException::departmentMismatch($departmentId);
        }
    }

    private function assertNoOverlapping(Staff $staff, string $schoolId): void
    {
        $overlapping = Assignment::query()
            ->forSchool($schoolId)
            ->forStaff($staff->getKey())
            ->ofType(AssignmentType::Department)
            ->current()
            ->exists();

        if ($overlapping) {
            throw AssignmentException::overlapping('department');
        }
    }
}
