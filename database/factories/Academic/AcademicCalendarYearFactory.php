<?php

declare(strict_types=1);

namespace Database\Factories\Academic;

use App\Domains\Academic\Domain\Enums\CalendarYearStatus;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicCalendarYear>
 */
class AcademicCalendarYearFactory extends Factory
{
    protected $model = AcademicCalendarYear::class;

    protected static int $sequence = 0;

    public function definition(): array
    {
        $startYear = 2000 + (self::$sequence++ % 200);
        $endYear = $startYear + 1;

        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'name' => "{$startYear}-{$endYear}",
            'code' => "AY-{$startYear}-{$endYear}",
            'start_date' => "{$startYear}-09-01",
            'end_date' => "{$endYear}-07-31",
            'description' => fake()->sentence(),
            'status' => CalendarYearStatus::Draft,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => [
            'status' => CalendarYearStatus::Active,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'status' => CalendarYearStatus::Draft,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'status' => CalendarYearStatus::Archived,
        ]);
    }
}
