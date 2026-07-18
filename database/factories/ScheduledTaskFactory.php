<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ScheduledTask;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduledTaskFactory extends Factory
{
    protected $model = ScheduledTask::class;

    public function definition(): array
    {
        return [
            'school_id' => fake()->uuid(),
            'name' => 'Task ' . fake()->word(),
            'command' => 'app:test-command',
            'schedule' => '* * * * *',
            'timezone' => 'UTC',
            'is_active' => true,
            'description' => fake()->sentence(),
            'metadata' => null,
            'last_run_at' => null,
            'next_run_at' => now()->addHour(),
            'last_exit_code' => null,
        ];
    }
}
