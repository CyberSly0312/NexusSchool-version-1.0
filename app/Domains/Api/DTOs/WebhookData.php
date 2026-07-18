<?php

declare(strict_types=1);

namespace App\Domains\Api\DTOs;

readonly class WebhookData
{
    public function __construct(
        public ?string $id,
        public string $apiClientId,
        public string $schoolId,
        public string $url,
        public string $secret,
        public array $events,
        public bool $isActive,
        public ?string $description,
        public int $failureCount,
        public ?string $lastTriggeredAt,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            apiClientId: (string) $data['api_client_id'],
            schoolId: (string) $data['school_id'],
            url: $data['url'],
            secret: $data['secret'],
            events: $data['events'],
            isActive: $data['is_active'] ?? true,
            description: $data['description'] ?? null,
            failureCount: $data['failure_count'] ?? 0,
            lastTriggeredAt: $data['last_triggered_at'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'api_client_id' => $this->apiClientId,
            'school_id' => $this->schoolId,
            'url' => $this->url,
            'secret' => $this->secret,
            'events' => $this->events,
            'is_active' => $this->isActive,
            'description' => $this->description,
            'failure_count' => $this->failureCount,
            'last_triggered_at' => $this->lastTriggeredAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
