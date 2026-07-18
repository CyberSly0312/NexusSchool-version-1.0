<?php

declare(strict_types=1);

namespace App\Domains\Api\DTOs;

readonly class WebhookDeliveryData
{
    public function __construct(
        public ?string $id,
        public string $webhookId,
        public string $schoolId,
        public string $event,
        public array $payload,
        public ?int $responseStatus,
        public ?string $responseBody,
        public bool $success,
        public ?string $deliveredAt,
        public int $retryCount,
        public int $maxRetries,
        public ?string $nextRetryAt,
        public ?string $createdAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            webhookId: $data['webhook_id'],
            schoolId: $data['school_id'],
            event: $data['event'],
            payload: $data['payload'],
            responseStatus: $data['response_status'] ?? null,
            responseBody: $data['response_body'] ?? null,
            success: $data['success'] ?? false,
            deliveredAt: $data['delivered_at'] ?? null,
            retryCount: $data['retry_count'] ?? 0,
            maxRetries: $data['max_retries'] ?? 3,
            nextRetryAt: $data['next_retry_at'] ?? null,
            createdAt: $data['created_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'webhook_id' => $this->webhookId,
            'school_id' => $this->schoolId,
            'event' => $this->event,
            'payload' => $this->payload,
            'response_status' => $this->responseStatus,
            'response_body' => $this->responseBody,
            'success' => $this->success,
            'delivered_at' => $this->deliveredAt,
            'retry_count' => $this->retryCount,
            'max_retries' => $this->maxRetries,
            'next_retry_at' => $this->nextRetryAt,
            'created_at' => $this->createdAt,
        ];
    }
}
