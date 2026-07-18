<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'registration_number' => 'ABC-'.strtoupper($this->faker->bothify('??-####')),
            'vin' => strtoupper($this->faker->bothify('?????????????????')),
            'manufacturer' => $this->faker->randomElement(['Toyota', 'Hyundai', 'Nissan', 'Honda', 'Mercedes-Benz']),
            'model' => $this->faker->randomElement(['HiAce', 'Coaster', 'Bus', 'Sprinter', 'Minibus']),
            'year' => $this->faker->numberBetween(2015, 2025),
            'color' => $this->faker->safeColorName(),
            'capacity' => $this->faker->numberBetween(7, 50),
            'fuel_type' => $this->faker->randomElement(['diesel', 'petrol', 'electric']),
            'insurance_expiry' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
            'inspection_expiry' => $this->faker->dateTimeBetween('+1 month', '+6 months'),
            'insurance_document' => null,
            'status' => 'available',
            'notes' => null,
        ];
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
        ]);
    }

    public function inService(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_service',
        ]);
    }

    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'maintenance',
        ]);
    }

    public function retired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'retired',
        ]);
    }
}
