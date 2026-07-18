<?php

declare(strict_types=1);

namespace App\Domains\Workflow\DTOs;

readonly class WorkflowData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $name,
        public string $slug,
        public ?string $description,
        public ?string $category,
        public int $priority,
        public string $status,
        public bool $isActive,
        public int $currentVersion,
        public ?array $metadata,
        public ?array $settings,
        public ?string $createdBy,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            schoolId: $data['school_id'],
            name: $data['name'],
            slug: $data['slug'] ?? $data['name'],
            description: $data['description'] ?? null,
            category: $data['category'] ?? null,
            priority: $data['priority'] ?? 0,
            status: $data['status'] ?? 'draft',
            isActive: $data['is_active'] ?? true,
            currentVersion: $data['current_version'] ?? 1,
            metadata: $data['metadata'] ?? null,
            settings: $data['settings'] ?? null,
            createdBy: $data['created_by'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
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
            'category' => $this->category,
            'priority' => $this->priority,
            'status' => $this->status,
            'is_active' => $this->isActive,
            'current_version' => $this->currentVersion,
            'metadata' => $this->metadata,
            'settings' => $this->settings,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
