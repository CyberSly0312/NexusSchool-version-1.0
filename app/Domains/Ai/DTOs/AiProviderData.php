<?php

declare(strict_types=1);

namespace App\Domains\Ai\DTOs;

readonly class AiProviderData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $name,
        public string $slug,
        public string $driver,
        public ?string $description,
        public bool $isActive,
        public ?array $configuration,
        public ?array $supportedModels,
        public ?string $defaultModel,
        public int $rateLimitPerMinute,
        public ?int $dailyTokenLimit,
        public ?int $monthlyTokenLimit,
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
            driver: $data['driver'],
            description: $data['description'] ?? null,
            isActive: $data['is_active'] ?? true,
            configuration: $data['configuration'] ?? null,
            supportedModels: $data['supported_models'] ?? null,
            defaultModel: $data['default_model'] ?? null,
            rateLimitPerMinute: $data['rate_limit_per_minute'] ?? 60,
            dailyTokenLimit: $data['daily_token_limit'] ?? null,
            monthlyTokenLimit: $data['monthly_token_limit'] ?? null,
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
            'driver' => $this->driver,
            'description' => $this->description,
            'is_active' => $this->isActive,
            'configuration' => $this->configuration,
            'supported_models' => $this->supportedModels,
            'default_model' => $this->defaultModel,
            'rate_limit_per_minute' => $this->rateLimitPerMinute,
            'daily_token_limit' => $this->dailyTokenLimit,
            'monthly_token_limit' => $this->monthlyTokenLimit,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
