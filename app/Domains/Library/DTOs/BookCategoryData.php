<?php

declare(strict_types=1);

namespace App\Domains\Library\DTOs;

readonly class BookCategoryData
{
    public function __construct(
        public string $schoolId,
        public string $name,
        public ?string $description = null,
        public ?int $parentId = null,
        public string $status = 'active',
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            name: $data['name'],
            description: $data['description'] ?? null,
            parentId: $data['parent_id'] ?? null,
            status: $data['status'] ?? 'active',
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parentId,
            'status' => $this->status,
        ];
    }
}
