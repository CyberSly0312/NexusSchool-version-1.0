<?php

declare(strict_types=1);

namespace App\Domains\Ai\DTOs;

readonly class AiKnowledgeSourceData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $name,
        public string $slug,
        public string $type,
        public ?string $description,
        public string $status,
        public ?string $sourcePath,
        public ?string $sourceType,
        public ?string $contentType,
        public ?int $contentSize,
        public ?string $contentHash,
        public int $version,
        public int $chunkCount,
        public int $totalTokens,
        public ?array $configuration,
        public ?array $metadata,
        public ?string $indexedAt,
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
            type: $data['type'],
            description: $data['description'] ?? null,
            status: $data['status'] ?? 'pending',
            sourcePath: $data['source_path'] ?? null,
            sourceType: $data['source_type'] ?? null,
            contentType: $data['content_type'] ?? null,
            contentSize: $data['content_size'] ?? null,
            contentHash: $data['content_hash'] ?? null,
            version: $data['version'] ?? 1,
            chunkCount: $data['chunk_count'] ?? 0,
            totalTokens: $data['total_tokens'] ?? 0,
            configuration: $data['configuration'] ?? null,
            metadata: $data['metadata'] ?? null,
            indexedAt: $data['indexed_at'] ?? null,
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
            'type' => $this->type,
            'description' => $this->description,
            'status' => $this->status,
            'source_path' => $this->sourcePath,
            'source_type' => $this->sourceType,
            'content_type' => $this->contentType,
            'content_size' => $this->contentSize,
            'content_hash' => $this->contentHash,
            'version' => $this->version,
            'chunk_count' => $this->chunkCount,
            'total_tokens' => $this->totalTokens,
            'configuration' => $this->configuration,
            'metadata' => $this->metadata,
            'indexed_at' => $this->indexedAt,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
