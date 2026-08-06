<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\Contracts;

use App\Domains\Timetable\Domain\References\RoomReference;

/**
 * Read-only gateway to rooms. The Room Management engine owns rooms; this
 * contract is the seam through which the Timetable engine books them. Today the
 * adapter maps the legacy Classroom model and ids are opaque strings.
 */
interface RoomContract
{
    /**
     * @return list<RoomReference>
     */
    public function rooms(string $schoolId): array;

    public function room(string $schoolId, string $roomId): ?RoomReference;
}
