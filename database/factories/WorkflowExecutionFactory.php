<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Workflow;
use App\Models\WorkflowExecution;
use App\Models\WorkflowVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowExecutionFactory extends Factory
{
    protected $model = WorkflowExecution::class;

    public function definition(): array
    {
        return [
            'school_id' => fake()->uuid(),
            'workflow_id' => Workflow::factory(),
            'workflow_version_id' => WorkflowVersion::factory(),
            'trigger_id' => null,
            'status' => 'pending',
            'current_node_id' => 'start',
            'context' => null,
            'variables' => null,
            'result' => null,
            'error' => null,
            'started_at' => null,
            'completed_at' => null,
            'duration_ms' => null,
            'retry_count' => 0,
            'max_retries' => 3,
            'idempotency_key' => fake()->uuid(),
            'initiated_by' => null,
            'initiated_by_type' => 'manual',
        ];
    }

    public function running(): static
    {
        return $this->state(fn () => [
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => 'completed',
            'started_at' => now()->subMinute(),
            'completed_at' => now(),
            'duration_ms' => 60000,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => 'failed',
            'error' => 'Test error message',
            'started_at' => now()->subMinute(),
            'completed_at' => now(),
        ]);
    }
}
