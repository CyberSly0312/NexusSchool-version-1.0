<?php

namespace Database\Factories;

use App\Domains\Department\Enums\DepartmentStatus;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        $uniqueCode = strtoupper(fake()->bothify('??-###'));

        return [
            'school_id' => 1,
            'name' => 'Department-'.$uniqueCode,
            'code' => $uniqueCode,
            'description' => fake()->sentence(),
            'color' => fake()->hexColor(),
            'order' => fake()->numberBetween(1, 50),
            'head_id' => null,
            'status' => DepartmentStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => DepartmentStatus::Active,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => DepartmentStatus::Archived,
        ]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => [
            'school_id' => $schoolId,
        ]);
    }
}
