<?php

namespace Database\Factories;

use App\Models\AnalyticsSnapshot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnalyticsSnapshot>
 */
class AnalyticsSnapshotFactory extends Factory
{
    protected $model = AnalyticsSnapshot::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'snapshot_type' => fake()->randomElement(['enrollment', 'attendance', 'library', 'transport', 'communication']),
            'data' => [
                'total' => fake()->numberBetween(10, 500),
                'active' => fake()->numberBetween(5, 300),
            ],
            'period_start' => now()->startOfDay(),
            'period_end' => now()->endOfDay(),
            'generated_at' => now(),
            'is_immutable' => true,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function enrollment(): static
    {
        return $this->state(fn () => ['snapshot_type' => 'enrollment']);
    }

    public function attendance(): static
    {
        return $this->state(fn () => ['snapshot_type' => 'attendance']);
    }
}
