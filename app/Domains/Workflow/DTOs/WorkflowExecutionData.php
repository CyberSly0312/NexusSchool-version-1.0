<?php

declare(strict_types=1);

namespace App\Domains\Workflow\DTOs;

readonly class WorkflowExecutionData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $workflowId,
        public string $workflowVersionId,
        public ?string $triggerId,
        public string $status,
        public ?string $currentNodeId,
        public ?array $context,
        public ?array $variables,
        public ?array $result,
        public ?string $error,
        public ?string $startedAt,
        public ?string $completedAt,
        public ?int $durationMs,
        public int $retryCount,
        public int $maxRetries,
        public ?string $idempotencyKey,
        public ?string $initiatedBy,
        public ?string $initiatedByType,
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
            workflowVersionId: $data['workflow_version_id'],
            triggerId: $data['trigger_id'] ?? null,
            status: $data['status'] ?? 'pending',
            currentNodeId: $data['current_node_id'] ?? null,
            context: $data['context'] ?? null,
            variables: $data['variables'] ?? null,
            result: $data['result'] ?? null,
            error: $data['error'] ?? null,
            startedAt: $data['started_at'] ?? null,
            completedAt: $data['completed_at'] ?? null,
            durationMs: $data['duration_ms'] ?? null,
            retryCount: $data['retry_count'] ?? 0,
            maxRetries: $data['max_retries'] ?? 3,
            idempotencyKey: $data['idempotency_key'] ?? null,
            initiatedBy: $data['initiated_by'] ?? null,
            initiatedByType: $data['initiated_by_type'] ?? null,
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
            'workflow_version_id' => $this->workflowVersionId,
            'trigger_id' => $this->triggerId,
            'status' => $this->status,
            'current_node_id' => $this->currentNodeId,
            'context' => $this->context,
            'variables' => $this->variables,
            'result' => $this->result,
            'error' => $this->error,
            'started_at' => $this->startedAt,
            'completed_at' => $this->completedAt,
            'duration_ms' => $this->durationMs,
            'retry_count' => $this->retryCount,
            'max_retries' => $this->maxRetries,
            'idempotency_key' => $this->idempotencyKey,
            'initiated_by' => $this->initiatedBy,
            'initiated_by_type' => $this->initiatedByType,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
