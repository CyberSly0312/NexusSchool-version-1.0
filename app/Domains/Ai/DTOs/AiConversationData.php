<?php

declare(strict_types=1);

namespace App\Domains\Ai\DTOs;

readonly class AiConversationData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public ?string $aiProviderId,
        public ?string $userId,
        public ?string $title,
        public string $status,
        public ?string $contextType,
        public ?string $contextId,
        public ?string $model,
        public ?string $systemPrompt,
        public ?string $summary,
        public int $messageCount,
        public int $totalInputTokens,
        public int $totalOutputTokens,
        public int $contextWindowUsed,
        public int $contextWindowLimit,
        public ?array $metadata,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            schoolId: $data['school_id'],
            aiProviderId: $data['ai_provider_id'] ?? null,
            userId: $data['user_id'] ?? null,
            title: $data['title'] ?? null,
            status: $data['status'] ?? 'active',
            contextType: $data['context_type'] ?? null,
            contextId: $data['context_id'] ?? null,
            model: $data['model'] ?? null,
            systemPrompt: $data['system_prompt'] ?? null,
            summary: $data['summary'] ?? null,
            messageCount: $data['message_count'] ?? 0,
            totalInputTokens: $data['total_input_tokens'] ?? 0,
            totalOutputTokens: $data['total_output_tokens'] ?? 0,
            contextWindowUsed: $data['context_window_used'] ?? 0,
            contextWindowLimit: $data['context_window_limit'] ?? 128000,
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
            'ai_provider_id' => $this->aiProviderId,
            'user_id' => $this->userId,
            'title' => $this->title,
            'status' => $this->status,
            'context_type' => $this->contextType,
            'context_id' => $this->contextId,
            'model' => $this->model,
            'system_prompt' => $this->systemPrompt,
            'summary' => $this->summary,
            'message_count' => $this->messageCount,
            'total_input_tokens' => $this->totalInputTokens,
            'total_output_tokens' => $this->totalOutputTokens,
            'context_window_used' => $this->contextWindowUsed,
            'context_window_limit' => $this->contextWindowLimit,
            'metadata' => $this->metadata,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
