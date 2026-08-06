<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Contracts;

use App\Domains\Timetable\Domain\Contracts\RoomContract;
use App\Domains\Timetable\Domain\References\RoomReference;
use App\Models\Classroom;

/**
 * Maps the legacy Classroom model onto the RoomContract. Rooms are owned by the
 * future Room Management engine; until then classroom ids are exposed as opaque
 * strings so the engine stays decoupled. This is the only place in the Timetable
 * domain allowed to see room models.
 */
final class RoomAdapter implements RoomContract
{
    public function rooms(string $schoolId): array
    {
        return Classroom::query()
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(fn (Classroom $room): RoomReference => $this->toRoom($room))
            ->all();
    }

    public function room(string $schoolId, string $roomId): ?RoomReference
    {
        $room = Classroom::query()
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->find($roomId);

        return $room !== null ? $this->toRoom($room) : null;
    }

    private function toRoom(Classroom $room): RoomReference
    {
        return new RoomReference(
            id: (string) $room->getKey(),
            code: (string) ($room->code ?? $room->name),
            name: (string) $room->name,
            capacity: $room->capacity,
            type: $room->room_type?->value,
            isActive: $room->status === 'active',
        );
    }
}
