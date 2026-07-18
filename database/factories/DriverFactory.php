<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'license_number' => 'DL-'.strtoupper($this->faker->bothify('??-#####')),
            'license_expiry' => $this->faker->dateTimeBetween('+1 year', '+5 years'),
            'license_category' => $this->faker->randomElement(['B', 'C', 'D']),
            'address' => $this->faker->address(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => $this->faker->phoneNumber(),
            'date_of_birth' => $this->faker->dateTimeBetween('-40 years', '-25 years'),
            'national_id' => $this->faker->bothify('??-########'),
            'photo' => null,
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

    public function onTrip(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'on_trip',
        ]);
    }

    public function onLeave(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'on_leave',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
