<?php

namespace Database\Factories;

use App\Domains\Student\Enums\StudentStatus;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $uniqueNumber = strtoupper(fake()->bothify('STU-####'));
        $birthDate = fake()->dateTimeBetween('-18 years', '-10 years')->format('Y-m-d');

        return [
            'school_id' => '1',
            'student_number' => $uniqueNumber,
            'admission_number' => fake()->bothify('ADM-####'),
            'national_id' => fake()->bothify('GHA-########'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'middle_name' => fake()->optional(0.3)->firstName(),
            'preferred_name' => fake()->optional(0.2)->firstName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => $birthDate,
            'place_of_birth' => fake()->city(),
            'nationality' => fake()->country(),
            'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'religion' => fake()->randomElement(['Christianity', 'Islam', 'Hinduism', 'Buddhism', 'Traditional']),
            'photo' => null,
            'email' => fake()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'residential_address' => fake()->address(),
            'admission_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'allergies' => fake()->randomElement(['Peanuts', 'Dust', 'Pollen', 'Shellfish', 'Lactose', 'None']),
            'medical_notes' => fake()->sentence(),
            'user_id' => null,
            'notes' => fake()->sentence(),
            'status' => StudentStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => StudentStatus::Active,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => StudentStatus::Archived,
        ]);
    }

    public function graduated(): static
    {
        return $this->state(fn () => [
            'status' => StudentStatus::Graduated,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => [
            'status' => StudentStatus::Suspended,
        ]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => [
            'school_id' => $schoolId,
        ]);
    }

    public function linkedToUser(int $userId): static
    {
        return $this->state(fn () => [
            'user_id' => $userId,
        ]);
    }
}
