<?php

declare(strict_types=1);

namespace Database\Factories\Timetable;

use App\Domains\Timetable\Infrastructure\Models\TeachingPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeachingPeriod>
 */
class TeachingPeriodFactory extends Factory
{
    protected $model = TeachingPeriod::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'day_of_week' => fake()->numberBetween(0, 4),
            'period_number' => fake()->numberBetween(1, 8),
            'starts_at' => '08:00',
            'ends_at' => '09:00',
            'duration_minutes' => 60,
            'is_active' => true,
        ];
    }

    public function at(int $day, int $period, string $startsAt = '08:00', string $endsAt = '09:00'): static
    {
        return $this->state(fn (): array => [
            'day_of_week' => $day,
            'period_number' => $period,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);
    }
}
