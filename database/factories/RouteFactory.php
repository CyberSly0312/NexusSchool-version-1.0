<?php

namespace Database\Factories;

use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Route>
 */
class RouteFactory extends Factory
{
    protected $model = Route::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'name' => $this->faker->randomElement(['Route A - North', 'Route B - South', 'Route C - East', 'Route D - West', 'Route E - Central']),
            'description' => $this->faker->sentence(),
            'distance_km' => $this->faker->randomFloat(2, 2, 25),
            'estimated_duration_minutes' => $this->faker->numberBetween(15, 90),
            'fare' => round($this->faker->randomFloat(2, 50, 500), 2),
            'status' => 'active',
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }
}
