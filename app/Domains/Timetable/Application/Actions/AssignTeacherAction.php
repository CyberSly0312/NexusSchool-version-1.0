<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Actions;

use App\Domains\Timetable\Application\Services\ScheduleEntryValidator;
use App\Domains\Timetable\Domain\Contracts\StaffContract;
use App\Domains\Timetable\Domain\Events\TeacherAssigned;
use App\Domains\Timetable\Domain\Exceptions\ContractViolationException;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Domain\Exceptions\ScheduleEntryException;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Assigns a teacher to a schedule entry. The teacher must exist, be active and
 * teach the entry's subject; assigning must never double-book the teacher.
 */
final class AssignTeacherAction
{
    public function __construct(
        private readonly StaffContract $staff,
        private readonly ScheduleEntryValidator $validator,
    ) {
    }

    public function execute(int|string $entryId, int|string $staffId, ?User $actor = null): ScheduleEntry
    {
        $entry = $this->loadMutableEntry($entryId);
        $timetable = $entry->timetable()->firstOrFail();

        $teacher = $this->staff->teacher($timetable->school_id, $staffId);

        if ($teacher === null) {
            throw new ContractViolationException('The teacher does not exist or is inactive for this school.');
        }

        if (! $teacher->canTeach((int) $entry->academic_subject_id)) {
            throw new ContractViolationException("The teacher is not assigned to subject #{$entry->academic_subject_id}.");
        }

        $this->validator->assertSlotFree(
            $timetable,
            (int) $entry->day_of_week,
            (string) $entry->starts_at,
            (string) $entry->ends_at,
            excludeEntryId: (int) $entry->getKey(),
            teacherId: (int) $staffId,
        );

        return DB::transaction(function () use ($entry, $timetable, $staffId, $actor): ScheduleEntry {
            $entry->update(['staff_id' => $staffId]);

            TeacherAssigned::dispatch($entry->refresh(), $timetable, $staffId, $actor);

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
            throw new InvalidTimetableStateException('Teachers can only be assigned on a draft timetable.');
        }

        return $entry;
    }
}
