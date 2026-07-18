<?php

declare(strict_types=1);

namespace App\Domains\Ai\DTOs;

readonly class AiPromptTemplateData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $name,
        public string $slug,
        public string $category,
        public ?string $description,
        public string $systemPrompt,
        public string $userPromptTemplate,
        public ?array $variables,
        public string $status,
        public int $version,
        public ?string $targetModel,
        public float $temperature,
        public int $maxTokens,
        public ?float $topP,
        public ?array $metadata,
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
            category: $data['category'],
            description: $data['description'] ?? null,
            systemPrompt: $data['system_prompt'],
            userPromptTemplate: $data['user_prompt_template'],
            variables: $data['variables'] ?? null,
            status: $data['status'] ?? 'active',
            version: $data['version'] ?? 1,
            targetModel: $data['target_model'] ?? null,
            temperature: $data['temperature'] ?? 0.7,
            maxTokens: $data['max_tokens'] ?? 4096,
            topP: $data['top_p'] ?? null,
            metadata: $data['metadata'] ?? null,
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
            'category' => $this->category,
            'description' => $this->description,
            'system_prompt' => $this->systemPrompt,
            'user_prompt_template' => $this->userPromptTemplate,
            'variables' => $this->variables,
            'status' => $this->status,
            'version' => $this->version,
            'target_model' => $this->targetModel,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
            'top_p' => $this->topP,
            'metadata' => $this->metadata,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
