<?php

namespace Database\Factories;

use App\Domains\AcademicClass\Enums\AcademicClassStatus;
use App\Models\AcademicClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicClass>
 */
class AcademicClassFactory extends Factory
{
    protected $model = AcademicClass::class;

    public function definition(): array
    {
        $uniqueCode = strtoupper(fake()->bothify('??-###'));

        return [
            'academic_year_id' => 1,
            'program_id' => 1,
            'grade_level_id' => 1,
            'section_id' => 1,
            'name' => 'Class-'.$uniqueCode,
            'code' => $uniqueCode,
            'description' => fake()->sentence(),
            'capacity' => fake()->numberBetween(10, 50),
            'display_order' => fake()->numberBetween(1, 50),
            'status' => AcademicClassStatus::Active,
            'is_active' => true,
            'homeroom_teacher_id' => null,
            'classroom' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => AcademicClassStatus::Active,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => AcademicClassStatus::Archived,
        ]);
    }

    public function enabled(): static
    {
        return $this->state(fn () => [
            'is_active' => true,
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    public function forAcademicYear(int $academicYearId): static
    {
        return $this->state(fn () => [
            'academic_year_id' => $academicYearId,
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

    public function forSection(int $sectionId): static
    {
        return $this->state(fn () => [
            'section_id' => $sectionId,
        ]);
    }

    public function withOrder(int $order): static
    {
        return $this->state(fn () => [
            'display_order' => $order,
        ]);
    }

    public function withCapacity(int $capacity): static
    {
        return $this->state(fn () => [
            'capacity' => $capacity,
        ]);
    }
}
