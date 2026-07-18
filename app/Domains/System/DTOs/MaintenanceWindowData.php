<?php

declare(strict_types=1);

namespace App\Domains\System\DTOs;

readonly class MaintenanceWindowData
{
    public function __construct(
        public ?string $id,
        public ?string $schoolId,
        public string $name,
        public ?string $description,
        public string $type,
        public string $status,
        public bool $isReadonly,
        public string $startsAt,
        public string $endsAt,
        public ?string $actualStartAt,
        public ?string $actualEndAt,
        public ?array $affectedServices,
        public ?array $metadata,
        public ?string $createdBy,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            schoolId: $data['school_id'] ?? null,
            name: $data['name'],
            description: $data['description'] ?? null,
            type: $data['type'] ?? 'scheduled',
            status: $data['status'] ?? 'pending',
            isReadonly: $data['is_readonly'] ?? false,
            startsAt: $data['starts_at'],
            endsAt: $data['ends_at'],
            actualStartAt: $data['actual_start_at'] ?? null,
            actualEndAt: $data['actual_end_at'] ?? null,
            affectedServices: $data['affected_services'] ?? null,
            metadata: $data['metadata'] ?? null,
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
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'is_readonly' => $this->isReadonly,
            'starts_at' => $this->startsAt,
            'ends_at' => $this->endsAt,
            'actual_start_at' => $this->actualStartAt,
            'actual_end_at' => $this->actualEndAt,
            'affected_services' => $this->affectedServices,
            'metadata' => $this->metadata,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
