<?php

namespace Database\Factories;

use App\Models\Fine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fine>
 */
class FineFactory extends Factory
{
    protected $model = Fine::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'book_loan_id' => null,
            'borrower_id' => '1',
            'borrower_type' => 'student',
            'reason' => $this->faker->randomElement(['Overdue book', 'Damaged book', 'Lost book', 'Late return']),
            'amount' => $this->faker->randomFloat(2, 1, 50),
            'paid_amount' => 0,
            'waived_amount' => 0,
            'fine_date' => now(),
            'paid_date' => null,
            'waived_date' => null,
            'payment_method' => null,
            'reference_number' => null,
            'status' => 'pending',
            'processed_by' => null,
            'notes' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_amount' => 0,
            'waived_amount' => 0,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_amount' => $attributes['amount'],
            'paid_date' => now(),
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'mobile']),
            'reference_number' => $this->faker->bothify('PAY-#####'),
        ]);
    }

    public function partiallyPaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'partially_paid',
            'paid_amount' => round($attributes['amount'] / 2, 2),
            'paid_date' => now(),
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'mobile']),
            'reference_number' => $this->faker->bothify('PAY-#####'),
        ]);
    }

    public function waived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'waived',
            'waived_amount' => $attributes['amount'],
            'waived_date' => now(),
        ]);
    }
}
