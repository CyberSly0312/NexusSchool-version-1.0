<?php

namespace Database\Factories;

use App\Domains\Section\Enums\SectionStatus;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Section>
 */
class SectionFactory extends Factory
{
    protected $model = Section::class;

    public function definition(): array
    {
        $uniqueCode = strtoupper(fake()->bothify('??-###'));

        return [
            'grade_level_id' => 1,
            'name' => 'Section-'.$uniqueCode,
            'code' => $uniqueCode,
            'description' => fake()->sentence(),
            'order' => fake()->numberBetween(1, 50),
            'status' => SectionStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => SectionStatus::Active,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => SectionStatus::Archived,
        ]);
    }

    public function forGradeLevel(int $gradeLevelId): static
    {
        return $this->state(fn () => [
            'grade_level_id' => $gradeLevelId,
        ]);
    }
}
