<?php

declare(strict_types=1);

namespace App\Domains\Ai\DTOs;

readonly class AiMessageData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $aiConversationId,
        public string $role,
        public string $content,
        public int $inputTokens,
        public int $outputTokens,
        public ?string $model,
        public ?string $finishReason,
        public ?array $metadata,
        public bool $isArchived,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            schoolId: $data['school_id'],
            aiConversationId: $data['ai_conversation_id'],
            role: $data['role'],
            content: $data['content'],
            inputTokens: $data['input_tokens'] ?? 0,
            outputTokens: $data['output_tokens'] ?? 0,
            model: $data['model'] ?? null,
            finishReason: $data['finish_reason'] ?? null,
            metadata: $data['metadata'] ?? null,
            isArchived: $data['is_archived'] ?? false,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->schoolId,
            'ai_conversation_id' => $this->aiConversationId,
            'role' => $this->role,
            'content' => $this->content,
            'input_tokens' => $this->inputTokens,
            'output_tokens' => $this->outputTokens,
            'model' => $this->model,
            'finish_reason' => $this->finishReason,
            'metadata' => $this->metadata,
            'is_archived' => $this->isArchived,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
