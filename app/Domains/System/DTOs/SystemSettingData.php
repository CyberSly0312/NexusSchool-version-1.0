<?php

declare(strict_types=1);

namespace App\Domains\System\DTOs;

readonly class SystemSettingData
{
    public function __construct(
        public ?string $id,
        public ?string $schoolId,
        public string $group,
        public string $key,
        public ?string $value,
        public string $type,
        public bool $isEncrypted,
        public bool $isReadonly,
        public bool $isSystem,
        public ?string $description,
        public ?array $metadata,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            schoolId: $data['school_id'] ?? null,
            group: $data['group'],
            key: $data['key'],
            value: $data['value'] ?? null,
            type: $data['type'] ?? 'string',
            isEncrypted: $data['is_encrypted'] ?? false,
            isReadonly: $data['is_readonly'] ?? false,
            isSystem: $data['is_system'] ?? false,
            description: $data['description'] ?? null,
            metadata: $data['metadata'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->schoolId,
            'group' => $this->group,
            'key' => $this->key,
            'value' => $this->value,
            'type' => $this->type,
            'is_encrypted' => $this->isEncrypted,
            'is_readonly' => $this->isReadonly,
            'is_system' => $this->isSystem,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
