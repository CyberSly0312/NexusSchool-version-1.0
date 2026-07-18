<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleMaintenance>
 */
class VehicleMaintenanceFactory extends Factory
{
    protected $model = VehicleMaintenance::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'vehicle_id' => Vehicle::factory(),
            'type' => $this->faker->randomElement(['preventive', 'corrective', 'emergency', 'inspection']),
            'description' => $this->faker->sentence(),
            'cost' => round($this->faker->randomFloat(2, 50, 5000), 2),
            'service_provider' => $this->faker->company(),
            'service_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'next_service_date' => $this->faker->dateTimeBetween('+1 month', '+6 months'),
            'odometer_reading' => $this->faker->numberBetween(10000, 200000),
            'receipt_document' => null,
            'status' => 'completed',
            'completed_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'notes' => null,
        ];
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'scheduled',
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
