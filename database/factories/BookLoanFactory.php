<?php

namespace Database\Factories;

use App\Models\BookCopy;
use App\Models\BookLoan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookLoan>
 */
class BookLoanFactory extends Factory
{
    protected $model = BookLoan::class;

    public function definition(): array
    {
        $loanDate = $this->faker->dateTimeBetween('-30 days', 'now');
        $dueDate = (clone $loanDate)->modify('+14 days');

        return [
            'school_id' => '1',
            'book_copy_id' => BookCopy::factory(),
            'borrower_id' => '1',
            'borrower_type' => 'student',
            'loan_date' => $loanDate,
            'due_date' => $dueDate,
            'return_date' => null,
            'status' => 'active',
            'renewal_count' => 0,
            'issued_by' => null,
            'returned_to' => null,
            'notes' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'return_date' => null,
        ]);
    }

    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'returned',
            'return_date' => $this->faker->dateTimeBetween('-10 days', 'now'),
        ]);
    }

    public function overdue(): static
    {
        $loanDate = $this->faker->dateTimeBetween('-40 days', '-15 days');
        $dueDate = (clone $loanDate)->modify('+14 days');

        return $this->state(fn (array $attributes) => [
            'loan_date' => $loanDate,
            'due_date' => $dueDate,
            'status' => 'overdue',
            'return_date' => null,
        ]);
    }

    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'lost',
            'return_date' => null,
        ]);
    }
}
