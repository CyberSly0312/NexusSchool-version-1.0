<?php

declare(strict_types=1);

namespace App\Domains\System\DTOs;

readonly class AuditLogData
{
    public function __construct(
        public ?string $id,
        public ?string $schoolId,
        public string $auditableType,
        public string $auditableId,
        public string $action,
        public ?array $oldValues,
        public ?array $newValues,
        public ?string $event,
        public ?string $description,
        public ?string $ipAddress,
        public ?string $userAgent,
        public ?string $tags,
        public ?string $userId,
        public ?string $userType,
        public ?string $createdAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            schoolId: $data['school_id'] ?? null,
            auditableType: $data['auditable_type'],
            auditableId: $data['auditable_id'],
            action: $data['action'],
            oldValues: $data['old_values'] ?? null,
            newValues: $data['new_values'] ?? null,
            event: $data['event'] ?? null,
            description: $data['description'] ?? null,
            ipAddress: $data['ip_address'] ?? null,
            userAgent: $data['user_agent'] ?? null,
            tags: $data['tags'] ?? null,
            userId: $data['user_id'] ?? null,
            userType: $data['user_type'] ?? null,
            createdAt: $data['created_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->schoolId,
            'auditable_type' => $this->auditableType,
            'auditable_id' => $this->auditableId,
            'action' => $this->action,
            'old_values' => $this->oldValues,
            'new_values' => $this->newValues,
            'event' => $this->event,
            'description' => $this->description,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'tags' => $this->tags,
            'user_id' => $this->userId,
            'user_type' => $this->userType,
            'created_at' => $this->createdAt,
        ];
    }
}
