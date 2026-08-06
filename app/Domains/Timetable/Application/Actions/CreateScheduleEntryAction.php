<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Actions;

use App\Domains\Timetable\Application\DTOs\ScheduleEntryData;
use App\Domains\Timetable\Application\Services\ScheduleEntryValidator;
use App\Domains\Timetable\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Timetable\Domain\Contracts\RoomContract;
use App\Domains\Timetable\Domain\Contracts\StaffContract;
use App\Domains\Timetable\Domain\Enums\RecurrenceFrequency;
use App\Domains\Timetable\Domain\Events\ScheduleEntryCreated;
use App\Domains\Timetable\Domain\Exceptions\ContractViolationException;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Domain\ValueObjects\DayOfWeek;
use App\Domains\Timetable\Domain\ValueObjects\LessonDuration;
use App\Domains\Timetable\Domain\ValueObjects\PeriodNumber;
use App\Domains\Timetable\Domain\ValueObjects\TimeRange;
use App\Domains\Timetable\Infrastructure\Models\RecurringPattern;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Domains\Timetable\Infrastructure\Models\TimeSlot;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Adds a single schedule entry to a draft timetable. All referenced academic
 * resources are validated exclusively through the domain contracts; the class /
 * teacher / room no-double-booking invariants are enforced before persisting.
 */
final class CreateScheduleEntryAction
{
    public function __construct(
        private readonly AcademicFrameworkContract $academicFramework,
        private readonly StaffContract $staff,
        private readonly RoomContract $rooms,
        private readonly ScheduleEntryValidator $validator,
    ) {
    }

    public function execute(ScheduleEntryData $data, ?User $actor = null): ScheduleEntry
    {
        $timetable = $this->loadDraftTimetable($data->timetableId, $data->schoolId);

        new DayOfWeek($data->dayOfWeek);
        new PeriodNumber($data->periodNumber);
        new TimeRange($data->startsAt, $data->endsAt);
        new LessonDuration($data->durationMinutes ?? $data->inferredDurationMinutes());

        $this->validateSubjectAndClass($data);
        $this->validateTeacher($data);
        $this->validateRoom($data);

        $this->validator->assertSlotFree(
            $timetable,
            $data->dayOfWeek,
            $data->startsAt,
            $data->endsAt,
            classId: (int) $data->schoolClassId,
            sectionId: $data->classSectionId !== null ? (int) $data->classSectionId : null,
            teacherId: $data->staffId !== null ? (int) $data->staffId : null,
            roomId: $data->roomId,
        );

        return DB::transaction(function () use ($data, $timetable, $actor): ScheduleEntry {
            $timeSlotId = $data->timeSlotId;

            if ($data->isRecurring && $timeSlotId === null) {
                $timeSlotId = $this->resolveTimeSlot($timetable, $data)->getKey();
            }

            $entry = ScheduleEntry::create([
                ...$data->toArray(),
                'timetable_id' => $timetable->getKey(),
                'time_slot_id' => $timeSlotId,
            ]);

            if ($data->isRecurring) {
                RecurringPattern::create([
                    'school_id' => $timetable->school_id,
                    'timetable_id' => $timetable->getKey(),
                    'schedule_entry_id' => $entry->getKey(),
                    'time_slot_id' => $timeSlotId,
                    'frequency' => RecurrenceFrequency::Weekly,
                    'interval' => 1,
                    'occurs_on_days' => [$data->dayOfWeek],
                    'starts_on' => $timetable->academic_calendar_year_id !== null ? now()->startOfYear()->toDateString() : now()->toDateString(),
                    'ends_on' => null,
                ]);
            }

            $this->syncEntryCount($timetable);

            ScheduleEntryCreated::dispatch($entry->refresh(), $timetable, $actor);

            return $entry->refresh();
        });
    }

    private function loadDraftTimetable(int|string $timetableId, string $schoolId): Timetable
    {
        $timetable = Timetable::query()
            ->forSchool($schoolId)
            ->find($timetableId);

        if ($timetable === null) {
            throw new InvalidTimetableStateException('Timetable not found for this school.');
        }

        if (! $timetable->isDraft()) {
            throw new InvalidTimetableStateException('Schedule entries can only be added to a draft timetable.');
        }

        return $timetable;
    }

    private function validateSubjectAndClass(ScheduleEntryData $data): void
    {
        $subject = $this->academicFramework->subject($data->schoolId, $data->academicSubjectId);

        if ($subject === null) {
            throw new ContractViolationException('The subject does not exist for this school.');
        }

        $class = $this->academicFramework->class($data->schoolId, $data->schoolClassId);

        if ($class === null) {
            throw new ContractViolationException('The class does not exist for this school.');
        }

        if ($data->classSectionId !== null) {
            $section = $this->academicFramework->section($data->schoolId, $data->classSectionId);

            if ($section === null) {
                throw new ContractViolationException('The section does not exist for this school.');
            }
        }
    }

    private function validateTeacher(ScheduleEntryData $data): void
    {
        if ($data->staffId === null) {
            return;
        }

        $teacher = $this->staff->teacher($data->schoolId, $data->staffId);

        if ($teacher === null) {
            throw new ContractViolationException('The teacher does not exist or is inactive for this school.');
        }

        if (! $teacher->canTeach($data->academicSubjectId)) {
            throw new ContractViolationException("The teacher is not assigned to subject #{$data->academicSubjectId}.");
        }
    }

    private function validateRoom(ScheduleEntryData $data): void
    {
        if ($data->roomId === null) {
            return;
        }

        if ($this->rooms->room($data->schoolId, $data->roomId) === null) {
            throw new ContractViolationException('The room does not exist or is inactive for this school.');
        }
    }

    private function resolveTimeSlot(Timetable $timetable, ScheduleEntryData $data): TimeSlot
    {
        $slot = TimeSlot::query()
            ->forSchool($timetable->school_id)
            ->where('timetable_id', $timetable->getKey())
            ->where('day_of_week', $data->dayOfWeek)
            ->where('period_number', $data->periodNumber)
            ->where('starts_at', $data->startsAt)
            ->where('ends_at', $data->endsAt)
            ->first();

        if ($slot !== null) {
            return $slot;
        }

        $teachingPeriod = \App\Domains\Timetable\Infrastructure\Models\TeachingPeriod::query()
            ->forSchool($timetable->school_id)
            ->where('day_of_week', $data->dayOfWeek)
            ->where('period_number', $data->periodNumber)
            ->first();

        return TimeSlot::create([
            'school_id' => $timetable->school_id,
            'timetable_id' => $timetable->getKey(),
            'teaching_period_id' => $teachingPeriod?->getKey(),
            'day_of_week' => $data->dayOfWeek,
            'period_number' => $data->periodNumber,
            'starts_at' => $data->startsAt,
            'ends_at' => $data->endsAt,
            'label' => sprintf('P%d', $data->periodNumber),
        ]);
    }

    private function syncEntryCount(Timetable $timetable): void
    {
        $timetable->update([
            'total_entries' => $timetable->entries()->where('status', 'scheduled')->count(),
        ]);
    }
}
