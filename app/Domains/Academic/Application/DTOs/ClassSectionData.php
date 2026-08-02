<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\DTOs;

use App\Domains\Academic\Domain\Enums\EntityStatus;

final readonly class ClassSectionData
{
    public function __construct(
        public string $schoolId,
        public int $schoolClassId,
        public string $name,
        public ?string $code = null,
        public ?int $capacity = null,
        public EntityStatus $status = EntityStatus::Active,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            schoolClassId: (int) $data['school_class_id'],
            name: $data['name'],
            code: $data['code'] ?? null,
            capacity: isset($data['capacity']) ? (int) $data['capacity'] : null,
            status: isset($data['status']) ? EntityStatus::from($data['status']) : EntityStatus::Active,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'school_class_id' => $this->schoolClassId,
            'name' => $this->name,
            'code' => $this->code,
            'capacity' => $this->capacity,
            'status' => $this->status->value,
        ];
    }
}
