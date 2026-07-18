<?php

declare(strict_types=1);

namespace App\Domains\Classroom\DTOs;

readonly class ClassroomData
{
    public function __construct(
        public string $schoolId,
        public string $name,
        public string $code,
        public ?string $building = null,
        public ?string $floor = null,
        public int $capacity = 0,
        public string $roomType = 'standard_classroom',
        public ?string $description = null,
        public int $displayOrder = 0,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            name: $data['name'],
            code: $data['code'],
            building: $data['building'] ?? null,
            floor: $data['floor'] ?? null,
            capacity: $data['capacity'] ?? 0,
            roomType: $data['room_type'] ?? 'standard_classroom',
            description: $data['description'] ?? null,
            displayOrder: $data['display_order'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'name' => $this->name,
            'code' => $this->code,
            'building' => $this->building,
            'floor' => $this->floor,
            'capacity' => $this->capacity,
            'room_type' => $this->roomType,
            'description' => $this->description,
            'display_order' => $this->displayOrder,
            'status' => 'active',
        ];
    }
}
