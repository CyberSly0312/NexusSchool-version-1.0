<?php

declare(strict_types=1);

namespace Database\Factories\Academic;

use App\Domains\Academic\Domain\Enums\CalendarEventType;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicCalendarEvent>
 */
class AcademicCalendarEventFactory extends Factory
{
    protected $model = AcademicCalendarEvent::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'calendar_year_id' => null,
            'calendar_term_id' => null,
            'title' => fake()->sentence(3),
            'type' => CalendarEventType::Event,
            'starts_on' => fake()->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'ends_on' => null,
            'is_all_day' => true,
            'description' => fake()->optional()->paragraph(),
        ];
    }
}
