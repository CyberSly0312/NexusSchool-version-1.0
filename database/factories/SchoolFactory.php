<?php

namespace Database\Factories;

use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Enums\SchoolType;
use App\Domains\School\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<School>
 */
class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        $name = fake()->unique()->company().' School';

        return [
            'uuid' => Str::ulid(),
            'name' => $name,
            'slug' => Str::slug($name),
            'database' => 'school_'.Str::slug($name),
            'domain' => Str::slug($name).'.nexus.school',
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => fake()->country(),
            'postal_code' => fake()->postcode(),
            'timezone' => fake()->timezone(),
            'currency' => fake()->currencyCode(),
            'locale' => 'en',
            'logo_url' => null,
            'registration_number' => fake()->unique()->numerify('REG-#####'),
            'school_type' => fake()->randomElement(SchoolType::cases()),
            'status' => SchoolStatus::Active,
            'settings' => null,
            'metadata' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => SchoolStatus::Pending]);
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => SchoolStatus::Active]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => SchoolStatus::Inactive]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => SchoolStatus::Suspended]);
    }

    public function primary(): static
    {
        return $this->state(fn () => ['school_type' => SchoolType::Primary]);
    }

    public function secondary(): static
    {
        return $this->state(fn () => ['school_type' => SchoolType::Secondary]);
    }

    public function technical(): static
    {
        return $this->state(fn () => ['school_type' => SchoolType::Technical]);
    }
}
