<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $now = now();

        return [
            'school_id' => '1',
            'book_id' => Book::factory(),
            'borrower_id' => '1',
            'borrower_type' => 'student',
            'reservation_date' => $now,
            'expiry_date' => $now->copy()->addDays(7),
            'fulfilled_date' => null,
            'status' => 'pending',
            'notes' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'fulfilled_date' => null,
        ]);
    }

    public function fulfilled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'fulfilled',
            'fulfilled_date' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expiry_date' => now()->subDay(),
        ]);
    }
}
