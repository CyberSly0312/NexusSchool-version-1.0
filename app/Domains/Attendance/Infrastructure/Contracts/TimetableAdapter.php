<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Infrastructure\Contracts;

use App\Domains\Attendance\Domain\Contracts\TimetableContract;
use App\Domains\Attendance\Domain\References\TimeSlotReference;
use App\Domains\Timetable\Domain\Enums\ScheduleEntryStatus;
use App\Domains\Timetable\Domain\Enums\TimetableStatus;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use Illuminate\Database\Eloquent\Builder;

/**
 * Maps Timetable-domain schedule entries onto the TimetableContract. This is the
 * only place in the Attendance domain allowed to see Timetable models.
 */
final class TimetableAdapter implements TimetableContract
{
    public function slotsForClassOn(string $schoolId, int|string $classId, string $date): array
    {
        $dayOfWeek = ((int) date('w', strtotime($date)) + 6) % 7;

        return $this->query($schoolId, $classId)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('period_number')
            ->get()
            ->map(fn (ScheduleEntry $entry): TimeSlotReference => $this->toReference($entry))
            ->all();
    }

    public function slotsForClassBetween(string $schoolId, int|string $classId, string $fromDate, string $toDate): array
    {
        $start = strtotime($fromDate);
        $end = strtotime($toDate);
        $days = [];

        for ($ts = $start; $ts <= $end; $ts += 86400) {
            $days[] = ((int) date('w', $ts) + 6) % 7;
        }

        if ($days === []) {
            return [];
        }

        return $this->query($schoolId, $classId)
            ->whereIn('day_of_week', array_values(array_unique($days)))
            ->orderBy('day_of_week')
            ->orderBy('period_number')
            ->get()
            ->map(fn (ScheduleEntry $entry): TimeSlotReference => $this->toReference($entry))
            ->all();
    }

    private function query(string $schoolId, int|string $classId): Builder
    {
        return ScheduleEntry::query()
            ->forSchool($schoolId)
            ->where('school_class_id', $classId)
            ->where('status', ScheduleEntryStatus::Scheduled)
            ->whereNull('parent_entry_id')
            ->whereHas('timetable', function (Builder $query) {
                $query->where('status', TimetableStatus::Published);
            });
    }

    private function toReference(ScheduleEntry $entry): TimeSlotReference
    {
        return new TimeSlotReference(
            id: $entry->getKey(),
            classId: $entry->school_class_id,
            sectionId: $entry->class_section_id,
            subjectId: $entry->academic_subject_id,
            staffId: $entry->staff_id,
            dayOfWeek: (int) $entry->day_of_week,
            periodNumber: (int) $entry->period_number,
            startsAt: (string) $entry->starts_at,
            endsAt: (string) $entry->ends_at,
        );
    }
}
