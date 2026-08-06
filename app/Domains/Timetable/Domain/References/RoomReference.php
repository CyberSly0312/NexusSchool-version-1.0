<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Domain\References;

/**
 * Read-only projection of a room. The id is an opaque string because the Room
 * Management engine (future) owns rooms; today the adapter maps classrooms.
 * Free of any concrete model.
 */
final readonly class RoomReference
{
    public function __construct(
        public string $id,
        public string $code,
        public string $name,
        public ?int $capacity = null,
        public ?string $type = null,
        public bool $isActive = true,
    ) {
    }
}
