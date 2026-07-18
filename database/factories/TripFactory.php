<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\Route;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'vehicle_id' => Vehicle::factory(),
            'driver_id' => Driver::factory(),
            'route_id' => Route::factory(),
            'direction' => $this->faker->randomElement(['to_school', 'from_school']),
            'academic_date' => $this->faker->dateTimeBetween('-7 days', '+7 days'),
            'departure_time' => $this->faker->time('H:i', '08:00'),
            'arrival_time' => $this->faker->time('H:i', '09:00'),
            'actual_departure' => null,
            'actual_arrival' => null,
            'status' => 'scheduled',
            'notes' => null,
        ];
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'scheduled',
        ]);
    }

    public function started(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'started',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'actual_departure' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'actual_arrival' => $this->faker->dateTimeBetween('-30 minutes', 'now'),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
