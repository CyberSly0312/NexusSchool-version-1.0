<?php

declare(strict_types=1);

namespace App\Domains\SubjectCategory\DTOs;

readonly class SubjectCategoryData
{
    public function __construct(
        public string $schoolId,
        public string $name,
        public string $code,
        public ?string $description = null,
        public int $displayOrder = 0,
        public ?string $color = null,
        public ?string $icon = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            name: $data['name'],
            code: $data['code'],
            description: $data['description'] ?? null,
            displayOrder: $data['display_order'] ?? 0,
            color: $data['color'] ?? null,
            icon: $data['icon'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'display_order' => $this->displayOrder,
            'color' => $this->color,
            'icon' => $this->icon,
            'status' => 'active',
        ];
    }
}
