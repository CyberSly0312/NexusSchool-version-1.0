<?php

declare(strict_types=1);

namespace Database\Factories\Staff;

use App\Domains\Staff\Domain\Enums\StaffSpecialization;
use App\Domains\Staff\Domain\Enums\StaffStatus;
use App\Domains\Staff\Infrastructure\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Staff>
 */
class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'staff_number' => fake()->unique()->regexify('STF-[0-9A-F]{6}-[0-9]{4}'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'middle_name' => fake()->optional()->firstName(),
            'preferred_name' => fake()->optional()->firstName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-21 years')->format('Y-m-d'),
            'nationality' => fake()->country(),
            'photo' => null,
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->unique()->numerify('+###########'),
            'alternate_phone' => fake()->optional()->phoneNumber(),
            'residential_address' => fake()->optional()->address(),
            'national_id' => fake()->optional()->numerify('CIN-########'),
            'tax_number' => fake()->optional()->numerify('IFU-####'),
            'bank_name' => fake()->optional()->company(),
            'bank_account' => fake()->optional()->regexify('[A-Z]{2}[0-9]{2}[A-Za-z0-9]{8}'),
            'specialization' => StaffSpecialization::Other,
            'user_id' => null,
            'status' => StaffStatus::Active,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => ['status' => StaffStatus::Active]);
    }

    public function suspended(): static
    {
        return $this->state(fn (): array => ['status' => StaffStatus::Suspended]);
    }

    public function terminated(): static
    {
        return $this->state(fn (): array => ['status' => StaffStatus::Terminated]);
    }

    public function teacher(): static
    {
        return $this->state(fn (): array => ['specialization' => StaffSpecialization::Teacher]);
    }

    public function withStatus(StaffStatus $status): static
    {
        return $this->state(fn (): array => ['status' => $status]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }
}
