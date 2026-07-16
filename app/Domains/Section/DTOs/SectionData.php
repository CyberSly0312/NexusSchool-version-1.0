<?php

namespace App\Domains\Section\DTOs;

readonly class SectionData
{
    public function __construct(
        public int $gradeLevelId,
        public string $name,
        public string $code,
        public ?string $description = null,
        public int $order = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            gradeLevelId: $data['grade_level_id'],
            name: $data['name'],
            code: $data['code'],
            description: $data['description'] ?? null,
            order: $data['order'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'grade_level_id' => $this->gradeLevelId,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'order' => $this->order,
            'status' => 'active',
        ];
    }
}
