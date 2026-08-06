<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Contracts;

use App\Domains\Assessment\Domain\References\TimeSlotReference;

/**
 * Read-only gateway to the Timetable domain (SPR-009). An assessment session
 * can be scheduled onto an existing free slot, so the engine needs to know
 * which slots a class has on a given date.
 */
interface TimetableContract
{
    /**
     * Slots scheduled for a class on a concrete date.
     *
     * @return list<TimeSlotReference>
     */
    public function slotsForClassOn(string $schoolId, int|string $classId, string $date): array;

    public function slot(string $schoolId, int|string $slotId): ?TimeSlotReference;
}
