<?php

namespace Database\Factories;

use App\Domains\Analytics\Enums\KpiStatus;
use App\Models\Kpi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kpi>
 */
class KpiFactory extends Factory
{
    protected $model = Kpi::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'school_id' => '1',
            'name' => ucfirst($name),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['enrollment', 'attendance', 'finance', 'library', 'transport', 'general']),
            'formula' => null,
            'target_value' => fake()->randomFloat(2, 0, 100),
            'unit' => fake()->randomElement(['%', 'count', 'students', 'books', 'vehicles', null]),
            'status' => KpiStatus::Active,
            'calculation_frequency' => fake()->randomElement(['daily', 'weekly', 'monthly', null]),
            'last_calculated_at' => null,
            'cached_value' => null,
            'cached_at' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => ['school_id' => $schoolId]);
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => KpiStatus::Active]);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => KpiStatus::Archived]);
    }

    public function withCachedValue(float $value): static
    {
        return $this->state(fn () => [
            'cached_value' => $value,
            'cached_at' => now(),
            'last_calculated_at' => now(),
        ]);
    }
}
