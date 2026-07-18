<?php

namespace Database\Factories;

use App\Models\Route;
use App\Models\RouteStop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RouteStop>
 */
class RouteStopFactory extends Factory
{
    protected $model = RouteStop::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'route_id' => Route::factory(),
            'name' => $this->faker->randomElement(['Market Square', 'Hospital Junction', 'School Gate', 'Church Road', 'Traffic Light', 'Community Center', 'Police Station', 'Town Hall']),
            'stop_order' => $this->faker->numberBetween(1, 10),
            'latitude' => $this->faker->latitude(4.5, 5.5),
            'longitude' => $this->faker->longitude(-1.5, 1.0),
            'estimated_time_minutes' => $this->faker->numberBetween(5, 60),
            'address' => $this->faker->optional(0.7)->address(),
            'notes' => null,
        ];
    }
}
