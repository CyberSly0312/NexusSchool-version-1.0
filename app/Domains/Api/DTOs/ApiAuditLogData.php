<?php

declare(strict_types=1);

namespace App\Domains\Api\DTOs;

readonly class ApiAuditLogData
{
    public function __construct(
        public ?string $id,
        public ?string $apiClientId,
        public string $schoolId,
        public ?string $userId,
        public ?string $apiTokenId,
        public string $method,
        public string $path,
        public ?int $statusCode,
        public string $ipAddress,
        public ?string $userAgent,
        public ?string $requestBody,
        public ?int $responseTimeMs,
        public ?string $createdAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            apiClientId: $data['api_client_id'] ?? null,
            schoolId: $data['school_id'],
            userId: $data['user_id'] ?? null,
            apiTokenId: $data['api_token_id'] ?? null,
            method: $data['method'],
            path: $data['path'],
            statusCode: $data['status_code'] ?? null,
            ipAddress: $data['ip_address'],
            userAgent: $data['user_agent'] ?? null,
            requestBody: $data['request_body'] ?? null,
            responseTimeMs: $data['response_time_ms'] ?? null,
            createdAt: $data['created_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'api_client_id' => $this->apiClientId,
            'school_id' => $this->schoolId,
            'user_id' => $this->userId,
            'api_token_id' => $this->apiTokenId,
            'method' => $this->method,
            'path' => $this->path,
            'status_code' => $this->statusCode,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'request_body' => $this->requestBody,
            'response_time_ms' => $this->responseTimeMs,
            'created_at' => $this->createdAt,
        ];
    }
}
