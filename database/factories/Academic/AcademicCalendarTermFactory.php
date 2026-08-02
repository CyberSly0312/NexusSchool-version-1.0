<?php

declare(strict_types=1);

namespace Database\Factories\Academic;

use App\Domains\Academic\Domain\Enums\CalendarTermStatus;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicCalendarTerm>
 */
class AcademicCalendarTermFactory extends Factory
{
    protected $model = AcademicCalendarTerm::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'calendar_year_id' => null,
            'name' => fake()->randomElement(['Trimestre 1', 'Trimestre 2', 'Trimestre 3']),
            'code' => fake()->unique()->bothify('T##'),
            'starts_on' => '2025-09-01',
            'ends_on' => '2025-12-20',
            'status' => CalendarTermStatus::Planned,
        ];
    }
}
