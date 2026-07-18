<?php

declare(strict_types=1);

namespace App\Domains\Analytics\DTOs;

use App\Domains\Analytics\Enums\DashboardType;
use Illuminate\Support\Str;

readonly class DashboardData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $name,
        public string $slug = '',
        public ?string $description = null,
        public string $type = 'custom',
        public bool $isDefault = false,
        public bool $isActive = true,
        public ?array $layout = null,
        public ?int $createdBy = null,
    ) {
    }

    public static function create(array $data): self
    {
        $type = $data['type'] ?? 'custom';
        if ($type instanceof DashboardType) {
            $type = $type->value;
        }

        return new self(
            id: $data['id'] ?? null,
            schoolId: $data['school_id'],
            name: $data['name'],
            slug: $data['slug'] ?? Str::slug($data['name']),
            description: $data['description'] ?? null,
            type: $type,
            isDefault: $data['is_default'] ?? false,
            isActive: $data['is_active'] ?? true,
            layout: $data['layout'] ?? null,
            createdBy: $data['created_by'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->schoolId,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'type' => $this->type instanceof DashboardType
                ? $this->type->value : $this->type,
            'is_default' => $this->isDefault,
            'is_active' => $this->isActive,
            'layout' => $this->layout,
            'created_by' => $this->createdBy,
        ];
    }
}
