<?php

declare(strict_types=1);

namespace Database\Factories\Guardian;

use App\Domains\Guardian\Domain\Enums\GuardianStatus;
use App\Domains\Guardian\Domain\Enums\PreferredContactMethod;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guardian>
 */
class GuardianFactory extends Factory
{
    protected $model = Guardian::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'guardian_number' => fake()->unique()->regexify('GUA-[0-9]{6}'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'middle_name' => fake()->optional()->firstName(),
            'preferred_name' => fake()->optional()->firstName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->dateTimeBetween('-70 years', '-20 years')->format('Y-m-d'),
            'nationality' => fake()->country(),
            'relationship_type' => 'guardian',
            'email' => fake()->optional(0.8)->safeEmail(),
            'phone_number' => fake()->unique()->numerify('+##########'),
            'alternate_phone' => fake()->optional()->phoneNumber(),
            'occupation' => fake()->optional()->jobTitle(),
            'employer' => fake()->optional()->company(),
            'residential_address' => fake()->optional()->address(),
            'postal_address' => fake()->optional()->address(),
            'emergency_contact' => fake()->optional()->phoneNumber(),
            'preferred_communication_language' => fake()->optional()->randomElement(['English', 'French']),
            'preferred_contact_method' => fake()->randomElement(PreferredContactMethod::cases()),
            'is_primary_guardian' => fake()->boolean(40),
            'user_id' => null,
            'notes' => fake()->optional()->sentence(),
            'status' => GuardianStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => ['status' => GuardianStatus::Active]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'status' => GuardianStatus::Archived,
            'deleted_at' => now(),
        ]);
    }

    public function forSchool(int $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }
}
