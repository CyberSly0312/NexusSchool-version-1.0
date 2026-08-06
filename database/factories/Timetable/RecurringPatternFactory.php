<?php

declare(strict_types=1);

namespace Database\Factories\Timetable;

use App\Domains\Timetable\Domain\Enums\RecurrenceFrequency;
use App\Domains\Timetable\Infrastructure\Models\RecurringPattern;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecurringPattern>
 */
class RecurringPatternFactory extends Factory
{
    protected $model = RecurringPattern::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'timetable_id' => \App\Domains\Timetable\Infrastructure\Models\Timetable::factory(),
            'schedule_entry_id' => \App\Domains\Timetable\Infrastructure\Models\ScheduleEntry::factory(),
            'frequency' => RecurrenceFrequency::Weekly,
            'interval' => 1,
            'occurs_on_days' => [0],
            'starts_on' => now()->startOfWeek(),
            'ends_on' => null,
            'end_after_occurrences' => null,
            'exceptions' => [],
        ];
    }
}
