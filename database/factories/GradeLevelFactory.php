<?php

namespace Database\Factories;

use App\Domains\GradeLevel\Enums\EducationalCycle;
use App\Domains\GradeLevel\Enums\GradeLevelStatus;
use App\Models\GradeLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradeLevel>
 */
class GradeLevelFactory extends Factory
{
    protected $model = GradeLevel::class;

    public function definition(): array
    {
        $uniqueCode = strtoupper(fake()->bothify('??-###'));

        return [
            'program_id' => 1,
            'name' => 'Grade-'.$uniqueCode,
            'code' => $uniqueCode,
            'short_name' => strtoupper(fake()->bothify('??')),
            'level_order' => fake()->numberBetween(1, 20),
            'educational_cycle' => EducationalCycle::Primary,
            'description' => fake()->sentence(),
            'status' => GradeLevelStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => GradeLevelStatus::Active,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => GradeLevelStatus::Archived,
        ]);
    }

    public function forProgram(int $programId): static
    {
        return $this->state(fn () => [
            'program_id' => $programId,
        ]);
    }

    public function withOrder(int $levelOrder): static
    {
        return $this->state(fn () => [
            'level_order' => $levelOrder,
        ]);
    }
}
