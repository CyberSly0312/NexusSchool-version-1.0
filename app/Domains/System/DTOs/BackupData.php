<?php

declare(strict_types=1);

namespace App\Domains\System\DTOs;

readonly class BackupData
{
    public function __construct(
        public ?string $id,
        public ?string $schoolId,
        public string $name,
        public string $type,
        public string $status,
        public string $disk,
        public ?string $path,
        public ?int $sizeBytes,
        public ?string $checksum,
        public ?array $tablesIncluded,
        public ?string $notes,
        public ?string $error,
        public ?string $createdBy,
        public ?string $startedAt,
        public ?string $completedAt,
        public ?int $durationMs,
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
            type: $data['type'] ?? 'manual',
            status: $data['status'] ?? 'pending',
            disk: $data['disk'] ?? 'local',
            path: $data['path'] ?? null,
            sizeBytes: $data['size_bytes'] ?? null,
            checksum: $data['checksum'] ?? null,
            tablesIncluded: $data['tables_included'] ?? null,
            notes: $data['notes'] ?? null,
            error: $data['error'] ?? null,
            createdBy: $data['created_by'] ?? null,
            startedAt: $data['started_at'] ?? null,
            completedAt: $data['completed_at'] ?? null,
            durationMs: $data['duration_ms'] ?? null,
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
            'type' => $this->type,
            'status' => $this->status,
            'disk' => $this->disk,
            'path' => $this->path,
            'size_bytes' => $this->sizeBytes,
            'checksum' => $this->checksum,
            'tables_included' => $this->tablesIncluded,
            'notes' => $this->notes,
            'error' => $this->error,
            'created_by' => $this->createdBy,
            'started_at' => $this->startedAt,
            'completed_at' => $this->completedAt,
            'duration_ms' => $this->durationMs,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
