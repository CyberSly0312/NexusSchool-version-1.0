<?php

namespace Database\Factories;

use App\Domains\Subject\Enums\SubjectStatus;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        $uniqueCode = strtoupper(fake()->bothify('??-###'));

        return [
            'school_id' => '1',
            'subject_category_id' => 1,
            'department_id' => null,
            'program_id' => null,
            'name' => 'Subject-'.$uniqueCode,
            'code' => $uniqueCode,
            'short_name' => fake()->optional(0.6)->lexify('???'),
            'description' => fake()->optional(0.5)->sentence(),
            'display_order' => fake()->numberBetween(1, 50),
            'color' => fake()->optional(0.6)->hexColor(),
            'is_elective' => false,
            'is_core_subject' => true,
            'weekly_hours' => fake()->numberBetween(1, 8),
            'status' => SubjectStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => SubjectStatus::Active,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => SubjectStatus::Archived,
        ]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => [
            'school_id' => $schoolId,
        ]);
    }

    public function forCategory(int $categoryId): static
    {
        return $this->state(fn () => [
            'subject_category_id' => $categoryId,
        ]);
    }

    public function forDepartment(int $departmentId): static
    {
        return $this->state(fn () => [
            'department_id' => $departmentId,
        ]);
    }

    public function forProgram(int $programId): static
    {
        return $this->state(fn () => [
            'program_id' => $programId,
        ]);
    }

    public function core(): static
    {
        return $this->state(fn () => [
            'is_core_subject' => true,
            'is_elective' => false,
        ]);
    }

    public function elective(): static
    {
        return $this->state(fn () => [
            'is_elective' => true,
            'is_core_subject' => false,
        ]);
    }

    public function withOrder(int $order): static
    {
        return $this->state(fn () => [
            'display_order' => $order,
        ]);
    }

    public function withWeeklyHours(int $hours): static
    {
        return $this->state(fn () => [
            'weekly_hours' => $hours,
        ]);
    }
}
