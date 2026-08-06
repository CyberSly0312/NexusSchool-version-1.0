<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Contracts;

use App\Domains\Assessment\Domain\Contracts\TimetableContract;
use App\Domains\Assessment\Domain\References\TimeSlotReference;
use App\Domains\Timetable\Domain\Enums\ScheduleEntryStatus;
use App\Domains\Timetable\Domain\Enums\TimetableStatus;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use Illuminate\Database\Eloquent\Builder;

/**
 * Maps Timetable-domain schedule entries onto the TimetableContract. This is the
 * only place in the Assessment domain allowed to see Timetable models.
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

    public function slot(string $schoolId, int|string $slotId): ?TimeSlotReference
    {
        $entry = ScheduleEntry::query()
            ->forSchool($schoolId)
            ->where('status', ScheduleEntryStatus::Scheduled)
            ->whereNull('parent_entry_id')
            ->whereHas('timetable', function (Builder $query) {
                $query->where('status', TimetableStatus::Published);
            })
            ->find($slotId);

        return $entry !== null ? $this->toReference($entry) : null;
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
