<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SystemHealth;
use Illuminate\Database\Eloquent\Factories\Factory;

class SystemHealthFactory extends Factory
{
    protected $model = SystemHealth::class;

    public function definition(): array
    {
        return [
            'school_id' => fake()->uuid(),
            'service' => fake()->randomElement(['database', 'cache', 'queue', 'storage', 'mail', 'scheduler']),
            'status' => 'healthy',
            'response_time_ms' => fake()->numberBetween(1, 500),
            'message' => null,
            'metadata' => null,
        ];
    }

    public function unhealthy(): static
    {
        return $this->state(fn () => ['status' => 'unhealthy', 'message' => 'Service unavailable']);
    }
}
