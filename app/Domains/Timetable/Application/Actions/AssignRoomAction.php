<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Actions;

use App\Domains\Timetable\Application\Services\ScheduleEntryValidator;
use App\Domains\Timetable\Domain\Contracts\RoomContract;
use App\Domains\Timetable\Domain\Events\RoomAssigned;
use App\Domains\Timetable\Domain\Exceptions\ContractViolationException;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Domain\Exceptions\ScheduleEntryException;
use App\Domains\Timetable\Infrastructure\Models\RoomAssignment;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Assigns a room to a schedule entry. The room must exist for the school and
 * must not already be double-booked on the same weekly position. The assignment
 * is journaled in a RoomAssignment row for room-usage queries.
 */
final class AssignRoomAction
{
    public function __construct(
        private readonly RoomContract $rooms,
        private readonly ScheduleEntryValidator $validator,
    ) {
    }

    public function execute(int|string $entryId, string $roomId, ?User $actor = null): ScheduleEntry
    {
        $entry = $this->loadMutableEntry($entryId);
        $timetable = $entry->timetable()->firstOrFail();

        if ($this->rooms->room($timetable->school_id, $roomId) === null) {
            throw new ContractViolationException('The room does not exist or is inactive for this school.');
        }

        $this->validator->assertSlotFree(
            $timetable,
            (int) $entry->day_of_week,
            (string) $entry->starts_at,
            (string) $entry->ends_at,
            excludeEntryId: (int) $entry->getKey(),
            roomId: $roomId,
        );

        return DB::transaction(function () use ($entry, $timetable, $roomId, $actor): ScheduleEntry {
            $entry->update(['room_id' => $roomId]);

            $assignment = RoomAssignment::query()
                ->forSchool($timetable->school_id)
                ->where('schedule_entry_id', $entry->getKey())
                ->first();

            $payload = [
                'school_id' => $timetable->school_id,
                'timetable_id' => $timetable->getKey(),
                'schedule_entry_id' => $entry->getKey(),
                'room_id' => $roomId,
                'day_of_week' => $entry->day_of_week,
                'period_number' => $entry->period_number,
                'starts_at' => $entry->starts_at,
                'ends_at' => $entry->ends_at,
                'purpose' => $entry->entry_type?->value ?? 'lesson',
            ];

            if ($assignment !== null) {
                $assignment->update($payload);
            } else {
                RoomAssignment::create($payload);
            }

            RoomAssigned::dispatch($entry->refresh(), $timetable, $roomId, $actor);

            return $entry->refresh();
        });
    }

    private function loadMutableEntry(int|string $entryId): ScheduleEntry
    {
        $entry = ScheduleEntry::query()->find($entryId);

        if ($entry === null) {
            throw new ScheduleEntryException('Schedule entry not found.');
        }

        $timetable = $entry->timetable()->firstOrFail();

        if (! $timetable->isDraft()) {
            throw new InvalidTimetableStateException('Rooms can only be assigned on a draft timetable.');
        }

        return $entry;
    }
}
