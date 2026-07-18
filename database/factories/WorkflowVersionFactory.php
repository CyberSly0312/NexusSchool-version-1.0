<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Workflow;
use App\Models\WorkflowVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowVersionFactory extends Factory
{
    protected $model = WorkflowVersion::class;

    public function definition(): array
    {
        return [
            'school_id' => fake()->uuid(),
            'workflow_id' => Workflow::factory(),
            'version' => 1,
            'status' => 'draft',
            'changelog' => fake()->sentence(),
            'nodes' => [
                ['id' => 'start', 'type' => 'start', 'name' => 'Start'],
                ['id' => 'end', 'type' => 'end', 'name' => 'End'],
            ],
            'edges' => [
                ['id' => 'edge-1', 'source' => 'start', 'target' => 'end'],
            ],
            'settings' => null,
            'published_by' => null,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => 'published',
            'published_by' => fake()->uuid(),
            'published_at' => now(),
        ]);
    }
}
