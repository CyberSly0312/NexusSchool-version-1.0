<?php

declare(strict_types=1);

namespace App\Domains\Workflow\DTOs;

readonly class AutomationRuleData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $name,
        public ?string $description,
        public ?string $category,
        public bool $isActive,
        public int $priority,
        public string $triggerType,
        public ?string $eventName,
        public ?array $conditions,
        public array $actions,
        public int $executionCount,
        public int $successCount,
        public int $failureCount,
        public ?string $lastExecutedAt,
        public int $cooldownSeconds,
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
            description: $data['description'] ?? null,
            category: $data['category'] ?? null,
            isActive: $data['is_active'] ?? true,
            priority: $data['priority'] ?? 0,
            triggerType: $data['trigger_type'],
            eventName: $data['event_name'] ?? null,
            conditions: $data['conditions'] ?? null,
            actions: $data['actions'] ?? [],
            executionCount: $data['execution_count'] ?? 0,
            successCount: $data['success_count'] ?? 0,
            failureCount: $data['failure_count'] ?? 0,
            lastExecutedAt: $data['last_executed_at'] ?? null,
            cooldownSeconds: $data['cooldown_seconds'] ?? 0,
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
            'description' => $this->description,
            'category' => $this->category,
            'is_active' => $this->isActive,
            'priority' => $this->priority,
            'trigger_type' => $this->triggerType,
            'event_name' => $this->eventName,
            'conditions' => $this->conditions,
            'actions' => $this->actions,
            'execution_count' => $this->executionCount,
            'success_count' => $this->successCount,
            'failure_count' => $this->failureCount,
            'last_executed_at' => $this->lastExecutedAt,
            'cooldown_seconds' => $this->cooldownSeconds,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
