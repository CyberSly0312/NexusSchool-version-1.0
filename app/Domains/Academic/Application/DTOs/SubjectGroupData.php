<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\DTOs;

use App\Domains\Academic\Domain\Enums\EntityStatus;

final readonly class SubjectGroupData
{
    public function __construct(
        public string $schoolId,
        public string $name,
        public ?string $code = null,
        public int $sortOrder = 0,
        public ?string $description = null,
        public EntityStatus $status = EntityStatus::Active,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            name: $data['name'],
            code: $data['code'] ?? null,
            sortOrder: (int) ($data['sort_order'] ?? 0),
            description: $data['description'] ?? null,
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
            'name' => $this->name,
            'code' => $this->code,
            'sort_order' => $this->sortOrder,
            'description' => $this->description,
            'status' => $this->status->value,
        ];
    }
}
