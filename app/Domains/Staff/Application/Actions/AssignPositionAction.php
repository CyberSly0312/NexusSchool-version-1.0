<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\Actions;

use App\Domains\Staff\Application\DTOs\AssignmentData;
use App\Domains\Staff\Domain\Enums\AssignmentType;
use App\Domains\Staff\Domain\Events\PositionAssigned;
use App\Domains\Staff\Domain\Exceptions\AssignmentException;
use App\Domains\Staff\Domain\Exceptions\InvalidStaffStateException;
use App\Domains\Staff\Infrastructure\Models\Assignment;
use App\Domains\Staff\Infrastructure\Models\Position;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Assigns a staff member to a position. The assignment is time-aware and
 * never rewrites the immutable employment history.
 */
final class AssignPositionAction
{
    public function execute(AssignmentData $data, ?User $actor = null): Assignment
    {
        $staff = Staff::query()->findOrFail($data->staffId);

        $this->assertMutable($staff);
        $this->assertPositionBelongsToSchool($data->positionId, $data->schoolId);
        $this->assertNoOverlapping($staff, $data->schoolId);

        $attributes = $data->toArray();

        return DB::transaction(function () use ($attributes, $actor): Assignment {
            $assignment = Assignment::create($attributes);

            PositionAssigned::dispatch($assignment, $actor);

            return $assignment;
        });
    }

    private function assertMutable(Staff $staff): void
    {
        if ($staff->isImmutable()) {
            throw InvalidStaffStateException::immutable($staff->getKey());
        }
    }

    private function assertPositionBelongsToSchool(?int $positionId, string $schoolId): void
    {
        if ($positionId === null) {
            throw AssignmentException::message('A position assignment requires a position.');
        }

        $exists = Position::query()
            ->forSchool($schoolId)
            ->whereKey($positionId)
            ->exists();

        if (! $exists) {
            throw AssignmentException::positionMismatch($positionId);
        }
    }

    private function assertNoOverlapping(Staff $staff, string $schoolId): void
    {
        $overlapping = Assignment::query()
            ->forSchool($schoolId)
            ->forStaff($staff->getKey())
            ->ofType(AssignmentType::Position)
            ->current()
            ->exists();

        if ($overlapping) {
            throw AssignmentException::overlapping('position');
        }
    }
}
