<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Actions;

use App\Domains\Timetable\Application\DTOs\GenerateTimetableData;
use App\Domains\Timetable\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\Timetable\Domain\Contracts\RoomContract;
use App\Domains\Timetable\Domain\Contracts\StaffContract;
use App\Domains\Timetable\Domain\Enums\RecurrenceFrequency;
use App\Domains\Timetable\Domain\Events\ScheduleEntryCreated;
use App\Domains\Timetable\Domain\Exceptions\ContractViolationException;
use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Domain\Exceptions\ScheduleEntryException;
use App\Domains\Timetable\Domain\References\ClassReference;
use App\Domains\Timetable\Domain\References\RoomReference;
use App\Domains\Timetable\Domain\References\SubjectReference;
use App\Domains\Timetable\Domain\References\TeacherReference;
use App\Domains\Timetable\Domain\ValueObjects\LessonDuration;
use App\Domains\Timetable\Infrastructure\Models\RecurringPattern;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Domains\Timetable\Infrastructure\Models\TeachingPeriod;
use App\Domains\Timetable\Infrastructure\Models\TimeSlot;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Deterministic auto-scheduler (SPR-007). Builds a draft timetable by placing
 * each class's weekly workload into the school's teaching grid, one lesson at a
 * time. Teachers and rooms are consumed greedily by id order so the output only
 * depends on the input — running the generator twice yields the same schedule.
 *
 * The engine only sees contract projections; lessons that cannot be placed are
 * simply skipped so the caller can run conflict detection afterwards.
 */
final class GenerateTimetableAction
{
    public function __construct(
        private readonly AcademicFrameworkContract $academicFramework,
        private readonly StaffContract $staff,
        private readonly RoomContract $rooms,
    ) {
    }

    public function execute(GenerateTimetableData $data, ?User $actor = null): Timetable
    {
        $timetable = $this->loadDraftTimetable($data->timetableId, $data->schoolId);

        $lessonDuration = $data->lessonDurationMinutes ?? (int) config('nexus-timetable.default_lesson_duration', 55);
        new LessonDuration($lessonDuration);

        $slots = $this->activeSlots($timetable->school_id);
        $classes = $this->resolveClasses($data);
        $teachers = $this->sortedTeachers($data->schoolId);
        $rooms = $this->sortedRooms($data->schoolId);

        if ($classes === []) {
            throw new ScheduleEntryException('No classes to schedule.');
        }

        $teachersBySubject = $this->indexTeachersBySubject($teachers);

        $placedClassSlots = [];
        $usedTeacherSlots = [];
        $usedRoomSlots = [];
        $teacherLoad = [];
        $created = 0;

        $termStart = $this->termStart($timetable);

        DB::transaction(function () use (
            &$created,
            &$placedClassSlots,
            &$usedTeacherSlots,
            &$usedRoomSlots,
            &$teacherLoad,
            $data,
            $timetable,
            $lessonDuration,
            $slots,
            $classes,
            $teachersBySubject,
            $rooms,
            $termStart,
            $actor,
        ): void {
            foreach ($classes as $class) {
                $subjects = $this->classSubjectsToSchedule($data, $class);

                foreach ($subjects as $subject) {
                    $weekly = $data->lessonsPerWeek(
                        $subject->id,
                        (int) config('nexus-timetable.default_weekly_lessons_per_subject', 2),
                    );

                    for ($occurrence = 0; $occurrence < $weekly; $occurrence++) {
                        $placed = $this->placeOne(
                            $timetable,
                            $class,
                            $subject,
                            $lessonDuration,
                            $slots,
                            $teachersBySubject,
                            $rooms,
                            $termStart,
                            $placedClassSlots,
                            $usedTeacherSlots,
                            $usedRoomSlots,
                            $teacherLoad,
                            $actor,
                        );

                        if ($placed) {
                            $created++;
                        }
                    }
                }
            }

            $timetable->update(['total_entries' => $created]);
        });

        return $timetable->refresh();
    }

    private function loadDraftTimetable(int|string $timetableId, string $schoolId): Timetable
    {
        $timetable = Timetable::query()->forSchool($schoolId)->find($timetableId);

        if ($timetable === null) {
            throw new InvalidTimetableStateException('Timetable not found for this school.');
        }

        if (! $timetable->isDraft()) {
            throw new InvalidTimetableStateException('Only a draft timetable can be generated.');
        }

        return $timetable;
    }

    /**
     * @return list<array{day: int, period: int, startsAt: string, endsAt: string}>
     */
    private function activeSlots(string $schoolId): array
    {
        $periods = TeachingPeriod::query()
            ->forSchool($schoolId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('period_number')
            ->get();

        return $periods
            ->map(fn (TeachingPeriod $period): array => [
                'day' => (int) $period->day_of_week,
                'period' => (int) $period->period_number,
                'startsAt' => (string) $period->starts_at,
                'endsAt' => (string) $period->ends_at,
            ])
            ->all();
    }

    /**
     * @return list<ClassReference>
     */
    private function resolveClasses(GenerateTimetableData $data): array
    {
        if ($data->classIds === null || $data->classIds === []) {
            return $this->academicFramework->classes($data->schoolId);
        }

        $classes = [];

        foreach (array_values($data->classIds) as $classId) {
            $class = $this->academicFramework->class($data->schoolId, $classId);

            if ($class === null) {
                throw new ContractViolationException("The class #{$classId} does not exist for this school.");
            }

            $classes[] = $class;
        }

        usort($classes, static fn (ClassReference $a, ClassReference $b): int => $a->id <=> $b->id);

        return $classes;
    }

    /**
     * @return list<TeacherReference>
     */
    private function sortedTeachers(string $schoolId): array
    {
        $teachers = $this->staff->teachers($schoolId);

        usort($teachers, static fn (TeacherReference $a, TeacherReference $b): int => $a->id <=> $b->id);

        return $teachers;
    }

    /**
     * @return list<RoomReference>
     */
    private function sortedRooms(string $schoolId): array
    {
        $rooms = $this->rooms->rooms($schoolId);

        usort($rooms, static fn (RoomReference $a, RoomReference $b): int => $a->id <=> $b->id);

        return $rooms;
    }

    /**
     * @param  list<TeacherReference>  $teachers
     * @return array<int|string, list<TeacherReference>>
     */
    private function indexTeachersBySubject(array $teachers): array
    {
        $index = [];

        foreach ($teachers as $teacher) {
            foreach ($teacher->subjectIds() as $subjectId) {
                $index[$subjectId][] = $teacher;
            }
        }

        return $index;
    }

    /**
     * @return list<SubjectReference>
     */
    private function classSubjectsToSchedule(GenerateTimetableData $data, ClassReference $class): array
    {
        $known = $this->academicFramework->classSubjects($data->schoolId, $class->id);
        $known = $this->sortBySubjectId($known);

        if ($data->requirements === null) {
            return $known;
        }

        $requiredIds = array_keys($data->requirements);

        $subjects = array_values(array_filter(
            $known,
            static fn (SubjectReference $subject): bool => in_array($subject->id, $requiredIds, true),
        ));

        $knownIds = array_map(static fn (SubjectReference $s): int|string => $s->id, $known);

        foreach ($requiredIds as $subjectId) {
            if (in_array($subjectId, $knownIds, true)) {
                continue;
            }

            $subject = $this->academicFramework->subject($data->schoolId, $subjectId);

            if ($subject !== null) {
                $subjects[] = $subject;
            }
        }

        return $this->sortBySubjectId($subjects);
    }

    /**
     * @param  list<SubjectReference>  $subjects
     * @return list<SubjectReference>
     */
    private function sortBySubjectId(array $subjects): array
    {
        usort($subjects, static fn (SubjectReference $a, SubjectReference $b): int => $a->id <=> $b->id);

        return $subjects;
    }

    /**
     * Places a single lesson on the first free grid slot. Returns true when a
     * lesson was created.
     *
     * @param  list<array{day: int, period: int, startsAt: string, endsAt: string}>  $slots
     * @param  array<int|string, list<TeacherReference>>  $teachersBySubject
     * @param  list<RoomReference>  $rooms
     * @param  array<string, bool>  $placedClassSlots
     * @param  array<string, bool>  $usedTeacherSlots
     * @param  array<string, bool>  $usedRoomSlots
     * @param  array<int|string, int>  $teacherLoad
     */
    private function placeOne(
        Timetable $timetable,
        ClassReference $class,
        SubjectReference $subject,
        int $lessonDuration,
        array $slots,
        array $teachersBySubject,
        array $rooms,
        string $termStart,
        array &$placedClassSlots,
        array &$usedTeacherSlots,
        array &$usedRoomSlots,
        array &$teacherLoad,
        ?User $actor,
    ): bool {
        $candidates = $teachersBySubject[$subject->id] ?? [];

        foreach ($slots as $slot) {
            $classSlotKey = "{$class->id}:{$slot['day']}:{$slot['period']}";

            if (isset($placedClassSlots[$classSlotKey])) {
                continue;
            }

            $teacher = $this->pickTeacher($candidates, $slot, $lessonDuration, $usedTeacherSlots, $teacherLoad);

            if ($teacher === null && $candidates !== []) {
                continue;
            }

            $room = $this->pickRoom($rooms, $slot, $usedRoomSlots);

            if ($teacher !== null) {
                $usedTeacherSlots["{$teacher->id}:{$slot['day']}:{$slot['period']}"] = true;
                $teacherLoad[$teacher->id] = ($teacherLoad[$teacher->id] ?? 0) + 1;
            }

            if ($room !== null) {
                $usedRoomSlots["{$room->id}:{$slot['day']}:{$slot['period']}"] = true;
            }

            $placedClassSlots[$classSlotKey] = true;

            $timeSlot = $this->resolveTimeSlot($timetable, $slot);

            $entry = ScheduleEntry::create([
                'school_id' => $timetable->school_id,
                'timetable_id' => $timetable->getKey(),
                'time_slot_id' => $timeSlot->getKey(),
                'academic_subject_id' => $subject->id,
                'school_class_id' => $class->id,
                'staff_id' => $teacher?->id,
                'room_id' => $room?->id,
                'day_of_week' => $slot['day'],
                'period_number' => $slot['period'],
                'starts_at' => $slot['startsAt'],
                'ends_at' => $slot['endsAt'],
                'duration_minutes' => $lessonDuration,
                'entry_type' => 'lesson',
                'status' => 'scheduled',
                'is_recurring' => true,
            ]);

            RecurringPattern::create([
                'school_id' => $timetable->school_id,
                'timetable_id' => $timetable->getKey(),
                'schedule_entry_id' => $entry->getKey(),
                'time_slot_id' => $timeSlot->getKey(),
                'frequency' => RecurrenceFrequency::Weekly,
                'interval' => 1,
                'occurs_on_days' => [$slot['day']],
                'starts_on' => $termStart,
                'ends_on' => null,
            ]);

            ScheduleEntryCreated::dispatch($entry, $timetable, $actor);

            return true;
        }

        return false;
    }

    /**
     * @param  list<TeacherReference>  $candidates
     * @param  array{day: int, period: int, startsAt: string, endsAt: string}  $slot
     * @param  array<string, bool>  $usedTeacherSlots
     * @param  array<int|string, int>  $teacherLoad
     */
    private function pickTeacher(
        array $candidates,
        array $slot,
        int $lessonDuration,
        array $usedTeacherSlots,
        array &$teacherLoad,
    ): ?TeacherReference {
        foreach ($candidates as $teacher) {
            if (isset($usedTeacherSlots["{$teacher->id}:{$slot['day']}:{$slot['period']}"])) {
                continue;
            }

            if (! $this->underLoad($teacher, $lessonDuration, $teacherLoad)) {
                continue;
            }

            return $teacher;
        }

        return null;
    }

    /**
     * @param  array<int|string, int>  $teacherLoad
     */
    private function underLoad(TeacherReference $teacher, int $lessonDuration, array $teacherLoad): bool
    {
        if ($teacher->maxWeeklyHours === null) {
            return true;
        }

        $allowed = (int) floor(($teacher->maxWeeklyHours * 60) / max(1, $lessonDuration));

        return ($teacherLoad[$teacher->id] ?? 0) < $allowed;
    }

    /**
     * @param  list<RoomReference>  $rooms
     * @param  array{day: int, period: int, startsAt: string, endsAt: string}  $slot
     * @param  array<string, bool>  $usedRoomSlots
     */
    private function pickRoom(array $rooms, array $slot, array $usedRoomSlots): ?RoomReference
    {
        foreach ($rooms as $room) {
            if (isset($usedRoomSlots["{$room->id}:{$slot['day']}:{$slot['period']}"])) {
                continue;
            }

            return $room;
        }

        return null;
    }

    /**
     * @param  array{day: int, period: int, startsAt: string, endsAt: string}  $slot
     */
    private function resolveTimeSlot(Timetable $timetable, array $slot): TimeSlot
    {
        $timeSlot = TimeSlot::query()
            ->forSchool($timetable->school_id)
            ->where('timetable_id', $timetable->getKey())
            ->where('day_of_week', $slot['day'])
            ->where('period_number', $slot['period'])
            ->first();

        if ($timeSlot !== null) {
            return $timeSlot;
        }

        $teachingPeriod = TeachingPeriod::query()
            ->forSchool($timetable->school_id)
            ->where('day_of_week', $slot['day'])
            ->where('period_number', $slot['period'])
            ->first();

        return TimeSlot::create([
            'school_id' => $timetable->school_id,
            'timetable_id' => $timetable->getKey(),
            'teaching_period_id' => $teachingPeriod?->getKey(),
            'day_of_week' => $slot['day'],
            'period_number' => $slot['period'],
            'starts_at' => $slot['startsAt'],
            'ends_at' => $slot['endsAt'],
            'label' => sprintf('P%d', $slot['period']),
        ]);
    }

    private function termStart(Timetable $timetable): string
    {
        if ($timetable->academic_calendar_term_id !== null) {
            $term = $this->academicFramework->term(
                $timetable->school_id,
                $timetable->academic_calendar_term_id,
            );

            if ($term !== null) {
                return $term->startsOn;
            }
        }

        if ($timetable->academic_calendar_year_id !== null) {
            $year = $this->academicFramework->year(
                $timetable->school_id,
                $timetable->academic_calendar_year_id,
            );

            if ($year !== null) {
                return $year->startDate;
            }
        }

        return now()->toDateString();
    }
}
