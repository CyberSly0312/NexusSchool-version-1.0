<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\Contracts;

use App\Domains\Attendance\Domain\References\TimeSlotReference;

/**
 * Read-only gateway to the Timetable domain (SPR-008). The Attendance engine
 * generates one session per class + time slot, so it needs to know which slots
 * a class actually has on a given date.
 */
interface TimetableContract
{
    /**
     * Slots scheduled for a class on a concrete date.
     *
     * @return list<TimeSlotReference>
     */
    public function slotsForClassOn(string $schoolId, int|string $classId, string $date): array;

    /**
     * Slots scheduled for a class inside a date window.
     *
     * @return list<TimeSlotReference>
     */
    public function slotsForClassBetween(string $schoolId, int|string $classId, string $fromDate, string $toDate): array;
}
