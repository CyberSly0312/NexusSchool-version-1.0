<?php

declare(strict_types=1);

namespace App\Domains\Api\DTOs;

readonly class ApiClientData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $name,
        public string $slug,
        public ?string $description,
        public string $secret,
        public ?string $callbackUrl,
        public bool $isActive,
        public ?array $scopes,
        public int $rateLimitPerMinute,
        public int $rateLimitPerDay,
        public ?string $createdBy,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            schoolId: (string) $data['school_id'],
            name: $data['name'],
            slug: $data['slug'] ?? $data['name'],
            description: $data['description'] ?? null,
            secret: $data['secret'],
            callbackUrl: $data['callback_url'] ?? null,
            isActive: $data['is_active'] ?? true,
            scopes: $data['scopes'] ?? null,
            rateLimitPerMinute: $data['rate_limit_per_minute'] ?? 60,
            rateLimitPerDay: $data['rate_limit_per_day'] ?? 10000,
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
            'secret' => $this->secret,
            'callback_url' => $this->callbackUrl,
            'is_active' => $this->isActive,
            'scopes' => $this->scopes,
            'rate_limit_per_minute' => $this->rateLimitPerMinute,
            'rate_limit_per_day' => $this->rateLimitPerDay,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
