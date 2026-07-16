<?php

namespace App\Domains\Department\DTOs;

readonly class DepartmentData
{
    public function __construct(
        public string $schoolId,
        public string $name,
        public string $code,
        public ?string $description = null,
        public ?string $color = null,
        public int $order = 0,
        public ?int $headId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            name: $data['name'],
            code: $data['code'],
            description: $data['description'] ?? null,
            color: $data['color'] ?? null,
            order: $data['order'] ?? 0,
            headId: $data['head_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'color' => $this->color,
            'order' => $this->order,
            'head_id' => $this->headId,
            'status' => 'active',
        ];
    }
}
