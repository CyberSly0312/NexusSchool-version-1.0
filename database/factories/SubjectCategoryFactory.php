<?php

namespace Database\Factories;

use App\Domains\SubjectCategory\Enums\SubjectCategoryStatus;
use App\Models\SubjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubjectCategory>
 */
class SubjectCategoryFactory extends Factory
{
    protected $model = SubjectCategory::class;

    public function definition(): array
    {
        $uniqueCode = strtoupper(fake()->bothify('??-###'));

        return [
            'school_id' => '1',
            'name' => 'Category-'.$uniqueCode,
            'code' => $uniqueCode,
            'description' => fake()->optional(0.5)->sentence(),
            'display_order' => fake()->numberBetween(1, 50),
            'color' => fake()->optional(0.6)->hexColor(),
            'icon' => fake()->optional(0.4)->randomElement(['academic-cap', 'beaker', 'book-open', 'globe-alt', 'calculator', 'musical-note']),
            'status' => SubjectCategoryStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => SubjectCategoryStatus::Active,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => SubjectCategoryStatus::Archived,
        ]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => [
            'school_id' => $schoolId,
        ]);
    }

    public function withOrder(int $order): static
    {
        return $this->state(fn () => [
            'display_order' => $order,
        ]);
    }

    public function withColor(string $color): static
    {
        return $this->state(fn () => [
            'color' => $color,
        ]);
    }

    public function withIcon(string $icon): static
    {
        return $this->state(fn () => [
            'icon' => $icon,
        ]);
    }
}
