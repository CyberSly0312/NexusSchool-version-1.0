<?php

namespace Database\Factories;

use App\Domains\SubjectCoefficient\Enums\SubjectCoefficientStatus;
use App\Models\SubjectCoefficient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubjectCoefficient>
 */
class SubjectCoefficientFactory extends Factory
{
    protected $model = SubjectCoefficient::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d');
        $endDate = fake()->optional(0.5)->dateTimeBetween($startDate, '+1 year');

        return [
            'school_id' => '1',
            'subject_id' => 1,
            'program_id' => 1,
            'grade_level_id' => 1,
            'academic_year_id' => null,
            'coefficient' => fake()->randomFloat(2, 0.1, 5),
            'effective_from' => $startDate,
            'effective_to' => $endDate?->format('Y-m-d'),
            'notes' => fake()->optional(0.5)->sentence(),
            'status' => SubjectCoefficientStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => SubjectCoefficientStatus::Active,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => SubjectCoefficientStatus::Archived,
        ]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => [
            'school_id' => $schoolId,
        ]);
    }

    public function forSubject(int $subjectId): static
    {
        return $this->state(fn () => [
            'subject_id' => $subjectId,
        ]);
    }

    public function forProgram(int $programId): static
    {
        return $this->state(fn () => [
            'program_id' => $programId,
        ]);
    }

    public function forGradeLevel(int $gradeLevelId): static
    {
        return $this->state(fn () => [
            'grade_level_id' => $gradeLevelId,
        ]);
    }

    public function forAcademicYear(?int $academicYearId): static
    {
        return $this->state(fn () => [
            'academic_year_id' => $academicYearId,
        ]);
    }

    public function withCoefficient(float $coefficient): static
    {
        return $this->state(fn () => [
            'coefficient' => $coefficient,
        ]);
    }

    public function withEffectiveFrom(string $date): static
    {
        return $this->state(fn () => [
            'effective_from' => $date,
        ]);
    }

    public function withEffectiveTo(?string $date): static
    {
        return $this->state(fn () => [
            'effective_to' => $date,
        ]);
    }
}
