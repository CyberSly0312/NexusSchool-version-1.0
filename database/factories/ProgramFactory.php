<?php

namespace Database\Factories;

use App\Domains\Program\Enums\EducationalLevel;
use App\Domains\Program\Enums\ProgramStatus;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        $uniqueCode = strtoupper(fake()->bothify('??-###'));

        return [
            'school_id' => 1,
            'department_id' => null,
            'name' => 'Program-'.$uniqueCode,
            'code' => $uniqueCode,
            'description' => fake()->sentence(),
            'educational_level' => fake()->randomElement(EducationalLevel::cases()),
            'duration' => fake()->randomElement(['1 year', '2 years', '3 years', '4 years', '6 months']),
            'order' => fake()->numberBetween(1, 50),
            'status' => ProgramStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => ProgramStatus::Active,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => ProgramStatus::Archived,
        ]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => [
            'school_id' => $schoolId,
        ]);
    }

    public function forDepartment(int $departmentId): static
    {
        return $this->state(fn () => [
            'department_id' => $departmentId,
        ]);
    }
}
