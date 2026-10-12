<?php

declare(strict_types=1);

namespace Database\Factories\Timetable;

use App\Domains\Timetable\Infrastructure\Models\TimeSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeSlot>
 */
class TimeSlotFactory extends Factory
{
    protected $model = TimeSlot::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'timetable_id' => \App\Domains\Timetable\Infrastructure\Models\Timetable::factory(),
            'teaching_period_id' => null,
            'day_of_week' => 0,
            'period_number' => 1,
            'starts_at' => '08:00',
            'ends_at' => '09:00',
            'label' => 'P1',
        ];
    }
}
