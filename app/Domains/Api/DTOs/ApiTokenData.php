<?php

declare(strict_types=1);

namespace App\Domains\Api\DTOs;

readonly class ApiTokenData
{
    public function __construct(
        public ?string $id,
        public string $apiClientId,
        public string $schoolId,
        public ?string $userId,
        public string $name,
        public string $token,
        public ?array $abilities,
        public ?string $expiresAt,
        public ?string $lastUsedAt,
        public bool $isActive,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            apiClientId: $data['api_client_id'],
            schoolId: (string) $data['school_id'],
            userId: isset($data['user_id']) ? (string) $data['user_id'] : null,
            name: $data['name'],
            token: $data['token'],
            abilities: $data['abilities'] ?? null,
            expiresAt: $data['expires_at'] ?? null,
            lastUsedAt: $data['last_used_at'] ?? null,
            isActive: $data['is_active'] ?? true,
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
            'user_id' => $this->userId,
            'name' => $this->name,
            'token' => $this->token,
            'abilities' => $this->abilities,
            'expires_at' => $this->expiresAt,
            'last_used_at' => $this->lastUsedAt,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
