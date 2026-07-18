<?php

declare(strict_types=1);

namespace App\Domains\Workflow\DTOs;

readonly class WorkflowTaskData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $workflowExecutionId,
        public string $nodeId,
        public string $type,
        public string $name,
        public string $status,
        public int $order,
        public ?string $assignedTo,
        public ?string $assignedToType,
        public ?string $assignedBy,
        public ?string $assignedAt,
        public ?array $inputData,
        public ?array $outputData,
        public ?string $notes,
        public ?string $error,
        public ?string $startedAt,
        public ?string $completedAt,
        public ?string $dueAt,
        public ?int $durationMs,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            schoolId: $data['school_id'],
            workflowExecutionId: $data['workflow_execution_id'],
            nodeId: $data['node_id'],
            type: $data['type'],
            name: $data['name'],
            status: $data['status'] ?? 'pending',
            order: $data['order'] ?? 0,
            assignedTo: $data['assigned_to'] ?? null,
            assignedToType: $data['assigned_to_type'] ?? null,
            assignedBy: $data['assigned_by'] ?? null,
            assignedAt: $data['assigned_at'] ?? null,
            inputData: $data['input_data'] ?? null,
            outputData: $data['output_data'] ?? null,
            notes: $data['notes'] ?? null,
            error: $data['error'] ?? null,
            startedAt: $data['started_at'] ?? null,
            completedAt: $data['completed_at'] ?? null,
            dueAt: $data['due_at'] ?? null,
            durationMs: $data['duration_ms'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->schoolId,
            'workflow_execution_id' => $this->workflowExecutionId,
            'node_id' => $this->nodeId,
            'type' => $this->type,
            'name' => $this->name,
            'status' => $this->status,
            'order' => $this->order,
            'assigned_to' => $this->assignedTo,
            'assigned_to_type' => $this->assignedToType,
            'assigned_by' => $this->assignedBy,
            'assigned_at' => $this->assignedAt,
            'input_data' => $this->inputData,
            'output_data' => $this->outputData,
            'notes' => $this->notes,
            'error' => $this->error,
            'started_at' => $this->startedAt,
            'completed_at' => $this->completedAt,
            'due_at' => $this->dueAt,
            'duration_ms' => $this->durationMs,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
