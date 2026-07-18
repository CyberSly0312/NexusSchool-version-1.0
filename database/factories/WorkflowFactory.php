<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Workflow;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WorkflowFactory extends Factory
{
    protected $model = Workflow::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'school_id' => fake()->uuid(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['approval', 'notification', 'automation', 'integration']),
            'priority' => fake()->numberBetween(0, 100),
            'status' => 'draft',
            'is_active' => true,
            'current_version' => 1,
            'metadata' => null,
            'settings' => null,
            'created_by' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active', 'is_active' => true]);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft']);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => 'archived', 'is_active' => false]);
    }
}
