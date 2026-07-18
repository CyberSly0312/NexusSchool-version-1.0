<?php

declare(strict_types=1);

namespace App\Domains\Workflow\DTOs;

readonly class WorkflowVersionData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $workflowId,
        public int $version,
        public string $status,
        public ?string $changelog,
        public array $nodes,
        public array $edges,
        public ?array $settings,
        public ?string $publishedBy,
        public ?string $publishedAt,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            schoolId: $data['school_id'],
            workflowId: $data['workflow_id'],
            version: $data['version'] ?? 1,
            status: $data['status'] ?? 'draft',
            changelog: $data['changelog'] ?? null,
            nodes: $data['nodes'] ?? [],
            edges: $data['edges'] ?? [],
            settings: $data['settings'] ?? null,
            publishedBy: $data['published_by'] ?? null,
            publishedAt: $data['published_at'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->schoolId,
            'workflow_id' => $this->workflowId,
            'version' => $this->version,
            'status' => $this->status,
            'changelog' => $this->changelog,
            'nodes' => $this->nodes,
            'edges' => $this->edges,
            'settings' => $this->settings,
            'published_by' => $this->publishedBy,
            'published_at' => $this->publishedAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
