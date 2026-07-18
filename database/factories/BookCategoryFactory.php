<?php

namespace Database\Factories;

use App\Models\BookCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookCategory>
 */
class BookCategoryFactory extends Factory
{
    protected $model = BookCategory::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'name' => $this->faker->randomElement(['Fiction', 'Science', 'Mathematics', 'History', 'Literature', 'Technology', 'Arts', 'Sports', 'Philosophy', 'Reference']),
            'description' => $this->faker->optional(0.6)->sentence(),
            'parent_id' => null,
            'status' => 'active',
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }
}
