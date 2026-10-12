<?php

declare(strict_types=1);

namespace Database\Factories\Student;

use App\Domains\Student\Domain\Enums\Gender;
use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\Enums\TransferStatus;
use App\Domains\Student\Infrastructure\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'student_number' => fake()->unique()->regexify('[A-Z]{2}-[0-9]{4}'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'middle_name' => fake()->optional()->firstName(),
            'preferred_name' => fake()->optional()->firstName(),
            'gender' => fake()->randomElement(Gender::cases()),
            'date_of_birth' => fake()->dateTimeBetween('-18 years', '-6 years')->format('Y-m-d'),
            'nationality' => fake()->country(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->optional()->phoneNumber(),
            'admission_date' => fake()->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
            'medical_notes' => fake()->optional()->sentence(),
            'status' => StudentStatus::Active,
            'transfer_status' => TransferStatus::None,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => ['status' => StudentStatus::Active]);
    }

    public function suspended(): static
    {
        return $this->state(fn (): array => [
            'status' => StudentStatus::Suspended,
            'suspended_at' => now(),
        ]);
    }

    public function withdrawn(): static
    {
        return $this->state(fn (): array => [
            'status' => StudentStatus::Withdrawn,
            'withdrawn_at' => now(),
        ]);
    }

    public function graduated(): static
    {
        return $this->state(fn (): array => [
            'status' => StudentStatus::Graduated,
            'graduated_at' => now()->toDateString(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'status' => StudentStatus::Archived,
            'archived_at' => now(),
            'deleted_at' => now(),
        ]);
    }
}
