<?php

declare(strict_types=1);

namespace App\Domains\Ai\DTOs;

readonly class AiUsageLogData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public ?string $aiProviderId,
        public ?string $aiConversationId,
        public ?string $userId,
        public string $action,
        public ?string $model,
        public int $inputTokens,
        public int $outputTokens,
        public int $totalTokens,
        public float $estimatedCost,
        public ?int $latencyMs,
        public bool $wasCached,
        public bool $isBillable,
        public string $status,
        public ?string $errorMessage,
        public ?array $metadata,
        public ?string $createdAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            schoolId: $data['school_id'],
            aiProviderId: $data['ai_provider_id'] ?? null,
            aiConversationId: $data['ai_conversation_id'] ?? null,
            userId: $data['user_id'] ?? null,
            action: $data['action'],
            model: $data['model'] ?? null,
            inputTokens: $data['input_tokens'] ?? 0,
            outputTokens: $data['output_tokens'] ?? 0,
            totalTokens: $data['total_tokens'] ?? 0,
            estimatedCost: $data['estimated_cost'] ?? 0,
            latencyMs: $data['latency_ms'] ?? null,
            wasCached: $data['was_cached'] ?? false,
            isBillable: $data['is_billable'] ?? true,
            status: $data['status'] ?? 'success',
            errorMessage: $data['error_message'] ?? null,
            metadata: $data['metadata'] ?? null,
            createdAt: $data['created_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->schoolId,
            'ai_provider_id' => $this->aiProviderId,
            'ai_conversation_id' => $this->aiConversationId,
            'user_id' => $this->userId,
            'action' => $this->action,
            'model' => $this->model,
            'input_tokens' => $this->inputTokens,
            'output_tokens' => $this->outputTokens,
            'total_tokens' => $this->totalTokens,
            'estimated_cost' => $this->estimatedCost,
            'latency_ms' => $this->latencyMs,
            'was_cached' => $this->wasCached,
            'is_billable' => $this->isBillable,
            'status' => $this->status,
            'error_message' => $this->errorMessage,
            'metadata' => $this->metadata,
            'created_at' => $this->createdAt,
        ];
    }
}
