<?php

declare(strict_types=1);

namespace Database\Factories\Timetable;

use App\Domains\Timetable\Domain\Enums\RoomAssignmentPurpose;
use App\Domains\Timetable\Infrastructure\Models\RoomAssignment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomAssignment>
 */
class RoomAssignmentFactory extends Factory
{
    protected $model = RoomAssignment::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'timetable_id' => \App\Domains\Timetable\Infrastructure\Models\Timetable::factory(),
            'schedule_entry_id' => null,
            'room_id' => (string) fake()->unique()->numberBetween(1, 99999),
            'day_of_week' => 0,
            'period_number' => 1,
            'purpose' => RoomAssignmentPurpose::Lesson,
        ];
    }
}
