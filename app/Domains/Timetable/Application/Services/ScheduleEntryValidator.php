<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Services;

use App\Domains\Timetable\Domain\Exceptions\ScheduleEntryException;
use App\Domains\Timetable\Domain\ValueObjects\TimeRange;
use App\Domains\Timetable\Infrastructure\Models\Timetable;

/**
 * Guards the no-double-booking invariants before an entry is created, moved or
 * assigned a teacher/room. All checks target scheduled entries only; cancelled
 * and rescheduled supersessions are ignored.
 */
final class ScheduleEntryValidator
{
    public function assertSlotFree(
        Timetable $timetable,
        int $dayOfWeek,
        string $startsAt,
        string $endsAt,
        ?int $excludeEntryId = null,
        ?int $classId = null,
        ?int $sectionId = null,
        ?int $teacherId = null,
        ?string $roomId = null,
    ): void {
        $range = new TimeRange($startsAt, $endsAt);

        $entries = $timetable->entries()
            ->where('day_of_week', $dayOfWeek)
            ->where('status', 'scheduled')
            ->when($excludeEntryId !== null, fn ($query) => $query->whereKeyNot($excludeEntryId))
            ->get();

        foreach ($entries as $entry) {
            $otherRange = new TimeRange((string) $entry->starts_at, (string) $entry->ends_at);

            if (! $range->overlaps($otherRange)) {
                continue;
            }

            if ($classId !== null && (int) $entry->school_class_id === $classId) {
                throw new ScheduleEntryException(
                    "La classe #{$classId} est déjà planifiée sur ce créneau (créneau #{$entry->getKey()})."
                );
            }

            if ($sectionId !== null && $entry->class_section_id !== null && (int) $entry->class_section_id === $sectionId) {
                throw new ScheduleEntryException(
                    "La section #{$sectionId} est déjà planifiée sur ce créneau (créneau #{$entry->getKey()})."
                );
            }

            if ($teacherId !== null && $entry->staff_id !== null && (int) $entry->staff_id === $teacherId) {
                throw new ScheduleEntryException(
                    "L'enseignant #{$teacherId} est déjà planifié sur ce créneau (créneau #{$entry->getKey()})."
                );
            }

            if ($roomId !== null && $entry->room_id !== null && $entry->room_id === $roomId) {
                throw new ScheduleEntryException(
                    "La salle [{$roomId}] est déjà utilisée sur ce créneau (créneau #{$entry->getKey()})."
                );
            }
        }
    }
}
