<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Actions;

use App\Domains\Timetable\Application\DTOs\AvailabilityConstraintData;
use App\Domains\Timetable\Domain\Events\AvailabilityConstraintCreated;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Infrastructure\Models\AvailabilityConstraint;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Blocks a resource (teacher, room or class) inside a timetable. The target is a
 * generic (type, id) pair, so the engine never touches the target's model.
 */
final class CreateAvailabilityConstraintAction
{
    public function execute(AvailabilityConstraintData $data, ?User $actor = null): AvailabilityConstraint
    {
        $timetable = Timetable::query()
            ->forSchool($data->schoolId)
            ->find($data->timetableId);

        if ($timetable === null) {
            throw new InvalidTimetableStateException('Timetable not found for this school.');
        }

        if (! $timetable->isDraft()) {
            throw new InvalidTimetableStateException('Constraints can only be added to a draft timetable.');
        }

        return DB::transaction(function () use ($data, $timetable, $actor): AvailabilityConstraint {
            $constraint = AvailabilityConstraint::create([
                'school_id' => $data->schoolId,
                'timetable_id' => $timetable->getKey(),
                'time_slot_id' => $data->timeSlotId,
                'constraint_type' => $data->constraintType->value,
                'target_type' => $data->constraintType->value,
                'target_id' => $data->targetId,
                'day_of_week' => $data->dayOfWeek,
                'starts_at' => $data->startsAt,
                'ends_at' => $data->endsAt,
                'starts_on' => $data->startsOn,
                'ends_on' => $data->endsOn,
                'reason' => $data->reason,
            ]);

            AvailabilityConstraintCreated::dispatch($constraint, $timetable, $actor);

            return $constraint;
        });
    }
}
