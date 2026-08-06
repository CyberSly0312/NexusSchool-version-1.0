<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Actions;

use App\Domains\Timetable\Application\DTOs\UpdateScheduleEntryData;
use App\Domains\Timetable\Application\Services\ScheduleEntryValidator;
use App\Domains\Timetable\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Timetable\Domain\Contracts\RoomContract;
use App\Domains\Timetable\Domain\Contracts\StaffContract;
use App\Domains\Timetable\Domain\Events\ScheduleEntryUpdated;
use App\Domains\Timetable\Domain\Exceptions\ContractViolationException;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Domain\Exceptions\ScheduleEntryException;
use App\Domains\Timetable\Domain\ValueObjects\DayOfWeek;
use App\Domains\Timetable\Domain\ValueObjects\LessonDuration;
use App\Domains\Timetable\Domain\ValueObjects\PeriodNumber;
use App\Domains\Timetable\Domain\ValueObjects\TimeRange;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Moves or edits a schedule entry on a draft timetable. Manual modifications are
 * blocked when they would create a double-booking; every accepted change is
 * recorded so manual overrides stay auditable.
 */
final class UpdateScheduleEntryAction
{
    public function __construct(
        private readonly AcademicFrameworkContract $academicFramework,
        private readonly StaffContract $staff,
        private readonly RoomContract $rooms,
        private readonly ScheduleEntryValidator $validator,
    ) {
    }

    public function execute(UpdateScheduleEntryData $data, ?User $actor = null): ScheduleEntry
    {
        $entry = ScheduleEntry::query()->find($data->entryId);

        if ($entry === null) {
            throw new ScheduleEntryException('Schedule entry not found.');
        }

        $timetable = $entry->timetable()->firstOrFail();

        if (! $timetable->isDraft()) {
            throw new InvalidTimetableStateException('Schedule entries can only be modified on a draft timetable.');
        }

        $next = $this->resolvedAttributes($data, $entry);

        new DayOfWeek((int) $next['day_of_week']);
        new PeriodNumber((int) $next['period_number']);
        new TimeRange((string) $next['starts_at'], (string) $next['ends_at']);
        new LessonDuration((int) $next['duration_minutes']);

        $this->validateReferences($timetable->school_id, $next);

        $this->validator->assertSlotFree(
            $timetable,
            (int) $next['day_of_week'],
            (string) $next['starts_at'],
            (string) $next['ends_at'],
            excludeEntryId: (int) $entry->getKey(),
            classId: $next['school_class_id'] !== null ? (int) $next['school_class_id'] : null,
            sectionId: $next['class_section_id'] !== null ? (int) $next['class_section_id'] : null,
            teacherId: $next['staff_id'] !== null ? (int) $next['staff_id'] : null,
            roomId: $next['room_id'],
        );

        return DB::transaction(function () use ($data, $entry, $timetable, $next, $actor): ScheduleEntry {
            $changes = $this->diff($entry, $next);

            $entry->update($next);

            $this->syncEntryCount($timetable);

            ScheduleEntryUpdated::dispatch($entry->refresh(), $timetable, $changes, $actor);

            return $entry->refresh();
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function resolvedAttributes(UpdateScheduleEntryData $data, ScheduleEntry $entry): array
    {
        return [
            'academic_subject_id' => $data->academicSubjectId ?? $entry->academic_subject_id,
            'school_class_id' => $data->schoolClassId ?? $entry->school_class_id,
            'class_section_id' => $data->classSectionId !== null ? $data->classSectionId : $entry->class_section_id,
            'staff_id' => $data->staffId !== null ? $data->staffId : $entry->staff_id,
            'room_id' => $data->roomId !== null ? $data->roomId : $entry->room_id,
            'day_of_week' => $data->dayOfWeek ?? $entry->day_of_week,
            'period_number' => $data->periodNumber ?? $entry->period_number,
            'starts_at' => $data->startsAt ?? $entry->starts_at,
            'ends_at' => $data->endsAt ?? $entry->ends_at,
            'duration_minutes' => $data->durationMinutes ?? $entry->duration_minutes,
            'entry_type' => $data->entryType?->value ?? $entry->entry_type?->value,
            'status' => $data->status?->value ?? $entry->status?->value,
            'notes' => $data->notes ?? $entry->notes,
        ];
    }

    /**
     * @param  array<string, mixed>  $next
     */
    private function validateReferences(string $schoolId, array $next): void
    {
        $subjectId = $next['academic_subject_id'];
        $classId = $next['school_class_id'];

        if ($this->academicFramework->subject($schoolId, $subjectId) === null) {
            throw new ContractViolationException('The subject does not exist for this school.');
        }

        if ($this->academicFramework->class($schoolId, $classId) === null) {
            throw new ContractViolationException('The class does not exist for this school.');
        }

        if ($next['staff_id'] !== null) {
            $teacher = $this->staff->teacher($schoolId, $next['staff_id']);

            if ($teacher === null) {
                throw new ContractViolationException('The teacher does not exist or is inactive for this school.');
            }

            if (! $teacher->canTeach($subjectId)) {
                throw new ContractViolationException("The teacher is not assigned to subject #{$subjectId}.");
            }
        }

        if ($next['room_id'] !== null && $this->rooms->room($schoolId, $next['room_id']) === null) {
            throw new ContractViolationException('The room does not exist or is inactive for this school.');
        }
    }

    /**
     * @param  array<string, mixed>  $next
     * @return array<string, mixed>
     */
    private function diff(ScheduleEntry $entry, array $next): array
    {
        $changes = [];

        foreach ($next as $attribute => $value) {
            $current = $entry->getRawOriginal($attribute);

            if ((string) $current !== (string) $value) {
                $changes[$attribute] = ['from' => $current, 'to' => $value];
            }
        }

        return $changes;
    }

    private function syncEntryCount(\App\Domains\Timetable\Infrastructure\Models\Timetable $timetable): void
    {
        $timetable->update([
            'total_entries' => $timetable->entries()->where('status', 'scheduled')->count(),
        ]);
    }
}
