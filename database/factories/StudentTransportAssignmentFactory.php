<?php

namespace Database\Factories;

use App\Models\Route;
use App\Models\StudentTransportAssignment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentTransportAssignment>
 */
class StudentTransportAssignmentFactory extends Factory
{
    protected $model = StudentTransportAssignment::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'student_id' => '1',
            'route_id' => Route::factory(),
            'pickup_stop_id' => null,
            'dropoff_stop_id' => null,
            'academic_year_id' => '1',
            'academic_term_id' => null,
            'monthly_fee' => round($this->faker->randomFloat(2, 50, 300), 2),
            'status' => 'active',
            'enrolled_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'notes' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }
}
