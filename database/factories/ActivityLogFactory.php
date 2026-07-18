<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'school_id' => fake()->uuid(),
            'causer_type' => 'App\\Models\\User',
            'causer_id' => fake()->uuid(),
            'subject_type' => 'App\\Models\\Student',
            'subject_id' => fake()->uuid(),
            'event' => fake()->randomElement(['created', 'updated', 'deleted']),
            'description' => fake()->sentence(),
            'properties' => null,
        ];
    }
}
