<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'isbn' => $this->faker->optional(0.7)->bothify('978-####-####-#'),
            'title' => $this->faker->sentence(4),
            'subtitle' => $this->faker->optional(0.4)->sentence(3),
            'description' => $this->faker->optional(0.6)->paragraphs(2, true),
            'language' => 'English',
            'edition' => $this->faker->optional(0.5)->randomElement(['1st', '2nd', '3rd', '4th', '5th']),
            'publication_year' => $this->faker->numberBetween(1990, 2026),
            'publisher_id' => null,
            'book_category_id' => null,
            'shelf_location' => $this->faker->optional(0.5)->bothify('SH-##-##'),
            'cover_image' => null,
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
