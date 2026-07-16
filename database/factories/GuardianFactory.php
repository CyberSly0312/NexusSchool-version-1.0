<?php

namespace Database\Factories;

use App\Domains\Guardian\Enums\GuardianStatus;
use App\Domains\Guardian\Enums\PreferredContactMethod;
use App\Domains\Guardian\Enums\RelationshipType;
use App\Models\Guardian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guardian>
 */
class GuardianFactory extends Factory
{
    protected $model = Guardian::class;

    public function definition(): array
    {
        $uniqueNumber = strtoupper(fake()->bothify('GUA-####'));

        return [
            'school_id' => '1',
            'guardian_number' => $uniqueNumber,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'middle_name' => fake()->firstName(),
            'preferred_name' => fake()->firstName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->dateTimeBetween('-70 years', '-20 years')->format('Y-m-d'),
            'nationality' => fake()->country(),
            'relationship_type' => fake()->randomElement(RelationshipType::cases())->value,
            'email' => fake()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'alternate_phone' => fake()->phoneNumber(),
            'occupation' => fake()->jobTitle(),
            'employer' => fake()->company(),
            'residential_address' => fake()->address(),
            'postal_address' => fake()->address(),
            'emergency_contact' => fake()->phoneNumber(),
            'preferred_communication_language' => fake()->randomElement(['English', 'French', 'Spanish']),
            'preferred_contact_method' => fake()->randomElement(PreferredContactMethod::cases())->value,
            'is_primary_guardian' => fake()->boolean(40),
            'user_id' => null,
            'notes' => fake()->sentence(),
            'status' => GuardianStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => GuardianStatus::Active,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => GuardianStatus::Archived,
        ]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => [
            'school_id' => $schoolId,
        ]);
    }

    public function primary(): static
    {
        return $this->state(fn () => [
            'is_primary_guardian' => true,
        ]);
    }

    public function secondary(): static
    {
        return $this->state(fn () => [
            'is_primary_guardian' => false,
        ]);
    }

    public function withRelationship(string $relationshipType): static
    {
        return $this->state(fn () => [
            'relationship_type' => $relationshipType,
        ]);
    }

    public function linkedToUser(int $userId): static
    {
        return $this->state(fn () => [
            'user_id' => $userId,
        ]);
    }
}
