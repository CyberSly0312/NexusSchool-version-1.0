<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookCopy>
 */
class BookCopyFactory extends Factory
{
    protected $model = BookCopy::class;

    public function definition(): array
    {
        $loanDate = $this->faker->dateTimeBetween('-2 years', 'now');

        return [
            'school_id' => '1',
            'book_id' => Book::factory(),
            'barcode' => $this->faker->unique()->bothify('BC-#####'),
            'inventory_number' => $this->faker->unique()->bothify('INV-#####'),
            'acquisition_date' => $loanDate,
            'purchase_cost' => $this->faker->randomFloat(2, 5, 50),
            'condition' => 'good',
            'is_available' => true,
            'status' => 'available',
            'notes' => null,
        ];
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => true,
            'status' => 'available',
        ]);
    }

    public function borrowed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
            'status' => 'borrowed',
        ]);
    }

    public function reserved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
            'status' => 'reserved',
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
            'status' => 'archived',
        ]);
    }

    public function forBook(int $bookId): static
    {
        return $this->state(fn (array $attributes) => [
            'book_id' => $bookId,
        ]);
    }
}
