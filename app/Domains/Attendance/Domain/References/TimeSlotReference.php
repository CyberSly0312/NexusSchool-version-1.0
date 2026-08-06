<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Domain\References;

/**
 * Read-only projection of a timetable slot (schedule entry) that an attendance
 * session can be generated for (SPR-008). Owned by the Timetable domain.
 */
final readonly class TimeSlotReference
{
    public function __construct(
        public int|string $id,
        public int|string $classId,
        public int|string|null $sectionId,
        public int|string|null $subjectId,
        public int|string|null $staffId,
        public int $dayOfWeek,
        public int $periodNumber,
        public string $startsAt,
        public string $endsAt,
    ) {
    }
}
