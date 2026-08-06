<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Services;

use App\Domains\Timetable\Domain\Exceptions\InvalidTimetableStateException;
use App\Domains\Timetable\Domain\ValueObjects\DayOfWeek;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Domains\Timetable\Infrastructure\Models\TeachingPeriod;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use Illuminate\Support\Collection;

/**
 * Read model of the Timetable engine: weekly grids and per-resource timetables.
 * Pure queries — they never mutate state.
 */
final class TimetableQueryService
{
    public function find(int|string $timetableId): Timetable
    {
        $timetable = Timetable::query()->find($timetableId);

        if ($timetable === null) {
            throw new InvalidTimetableStateException('Timetable not found.');
        }

        return $timetable;
    }

    /**
     * Full weekly grid of a timetable. Cells are grouped by day (Monday-first)
     * and period number; empty slots from the school's teaching grid are
     * included so the grid renders in full.
     *
     * @return array<string, mixed>
     */
    public function weeklyGrid(int|string $timetableId): array
    {
        return $this->gridFor($timetableId, null);
    }

    /**
     * @return array<string, mixed>
     */
    public function teacherTimetable(int|string $staffId, int|string $timetableId): array
    {
        return $this->gridFor($timetableId, static fn (ScheduleEntry $entry): bool => (int) $entry->staff_id === (int) $staffId);
    }

    /**
     * @return array<string, mixed>
     */
    public function classTimetable(int|string $classId, int|string $timetableId): array
    {
        return $this->gridFor($timetableId, static fn (ScheduleEntry $entry): bool => (int) $entry->school_class_id === (int) $classId);
    }

    /**
     * @return array<string, mixed>
     */
    public function sectionTimetable(int|string $sectionId, int|string $timetableId): array
    {
        return $this->gridFor($timetableId, static fn (ScheduleEntry $entry): bool => $entry->class_section_id !== null && (int) $entry->class_section_id === (int) $sectionId);
    }

    /**
     * @return array<string, mixed>
     */
    public function roomTimetable(string $roomId, int|string $timetableId): array
    {
        return $this->gridFor($timetableId, static fn (ScheduleEntry $entry): bool => $entry->room_id === $roomId);
    }

    /**
     * Workload summary for a teacher inside a timetable.
     *
     * @return array<string, mixed>
     */
    public function workload(int|string $staffId, int|string $timetableId): array
    {
        $entries = $this->find($timetableId)
            ->entries()
            ->where('staff_id', $staffId)
            ->where('status', 'scheduled')
            ->get();

        $totalMinutes = $entries->sum('duration_minutes');
        $perSubject = $entries->groupBy('academic_subject_id')
            ->map(fn (Collection $group): int => $group->count());

        return [
            'staff_id' => (int) $staffId,
            'lessons' => $entries->count(),
            'hours' => round($totalMinutes / 60, 2),
            'minutes' => $totalMinutes,
            'days' => $entries->pluck('day_of_week')->unique()->sort()->values()->all(),
            'subjects' => $perSubject->all(),
        ];
    }

    /**
     * Free teaching slots for a class: grid positions not yet occupied by the
     * class.
     *
     * @return list<array{day: int, period_number: int, starts_at: string, ends_at: string}>
     */
    public function openSlots(int|string $timetableId, int|string $classId): array
    {
        $timetable = $this->find($timetableId);

        $occupied = $timetable->entries()
            ->where('school_class_id', $classId)
            ->where('status', 'scheduled')
            ->get()
            ->map(fn (ScheduleEntry $entry): string => $entry->day_of_week.':'.$entry->period_number)
            ->flip();

        return TeachingPeriod::query()
            ->forSchool($timetable->school_id)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('period_number')
            ->get()
            ->reject(fn (TeachingPeriod $period): bool => $occupied->has($period->day_of_week.':'.$period->period_number))
            ->map(fn (TeachingPeriod $period): array => [
                'day' => (int) $period->day_of_week,
                'period_number' => (int) $period->period_number,
                'starts_at' => (string) $period->starts_at,
                'ends_at' => (string) $period->ends_at,
            ])
            ->values()
            ->all();
    }

    /**
     * Latest unresolved conflicts of a timetable.
     */
    public function unresolvedConflicts(int|string $timetableId): Collection
    {
        return $this->find($timetableId)
            ->conflicts()
            ->whereNull('resolved_at')
            ->orderBy('day_of_week')
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function gridFor(int|string $timetableId, ?callable $filter): array
    {
        $timetable = $this->find($timetableId);

        $periods = TeachingPeriod::query()
            ->forSchool($timetable->school_id)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('period_number')
            ->get();

        $entries = $timetable->entries()
            ->where('status', 'scheduled')
            ->orderBy('day_of_week')
            ->orderBy('period_number')
            ->get();

        $grid = [];

        foreach ($periods as $period) {
            $grid[$period->day_of_week][$period->period_number] = [
                'starts_at' => (string) $period->starts_at,
                'ends_at' => (string) $period->ends_at,
                'entries' => [],
            ];
        }

        foreach ($entries as $entry) {
            if ($filter !== null && ! $filter($entry)) {
                continue;
            }

            $day = (int) $entry->day_of_week;
            $period = (int) $entry->period_number;

            $grid[$day][$period] ??= [
                'starts_at' => (string) $entry->starts_at,
                'ends_at' => (string) $entry->ends_at,
                'entries' => [],
            ];

            $grid[$day][$period]['entries'][] = [
                'id' => $entry->getKey(),
                'uuid' => $entry->uuid,
                'academic_subject_id' => $entry->academic_subject_id,
                'school_class_id' => $entry->school_class_id,
                'class_section_id' => $entry->class_section_id,
                'staff_id' => $entry->staff_id,
                'room_id' => $entry->room_id,
                'period_number' => $period,
                'starts_at' => (string) $entry->starts_at,
                'ends_at' => (string) $entry->ends_at,
                'duration_minutes' => $entry->duration_minutes,
                'entry_type' => $entry->entry_type?->value,
            ];
        }

        $days = [];

        for ($day = DayOfWeek::MONDAY; $day <= DayOfWeek::SUNDAY; $day++) {
            if (! isset($grid[$day])) {
                continue;
            }

            ksort($grid[$day]);

            $days[] = [
                'day' => $day,
                'label' => (new DayOfWeek($day))->label(),
                'periods' => $grid[$day],
            ];
        }

        return [
            'timetable_id' => $timetable->getKey(),
            'days' => $days,
        ];
    }
}
