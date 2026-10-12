<?php

declare(strict_types=1);

namespace Database\Factories\Staff;

use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Infrastructure\Models\StaffEmergencyContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StaffEmergencyContact>
 */
class StaffEmergencyContactFactory extends Factory
{
    protected $model = StaffEmergencyContact::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'staff_id' => Staff::factory(),
            'name' => fake()->name(),
            'relationship' => fake()->randomElement(['Conjoint', 'Parent', 'Fratrie', 'Ami', 'Autre']),
            'phone_number' => fake()->unique()->numerify('+###########'),
            'email' => fake()->optional()->safeEmail(),
            'is_primary' => false,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (): array => ['is_primary' => true]);
    }

    public function forStaff(int $staffId): static
    {
        return $this->state(fn (): array => ['staff_id' => $staffId]);
    }
}
