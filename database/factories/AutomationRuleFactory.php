<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AutomationRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class AutomationRuleFactory extends Factory
{
    protected $model = AutomationRule::class;

    public function definition(): array
    {
        return [
            'school_id' => fake()->uuid(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['notification', 'data_sync', 'integration', 'maintenance']),
            'is_active' => true,
            'priority' => fake()->numberBetween(0, 100),
            'trigger_type' => 'event',
            'event_name' => fake()->randomElement([
                'student.created',
                'fee.overdue',
                'leave.requested',
                'transport.alert',
            ]),
            'conditions' => null,
            'actions' => [
                ['type' => 'notify', 'parameters' => ['channel' => 'email']],
            ],
            'execution_count' => 0,
            'success_count' => 0,
            'failure_count' => 0,
            'last_executed_at' => null,
            'cooldown_seconds' => 0,
            'created_by' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
