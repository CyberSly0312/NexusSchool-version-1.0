<?php

declare(strict_types=1);

namespace Database\Factories\Timetable;

use App\Domains\Timetable\Domain\Enums\ScheduleEntryStatus;
use App\Domains\Timetable\Domain\Enums\ScheduleEntryType;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScheduleEntry>
 */
class ScheduleEntryFactory extends Factory
{
    protected $model = ScheduleEntry::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'timetable_id' => \App\Domains\Timetable\Infrastructure\Models\Timetable::factory(),
            'time_slot_id' => null,
            'academic_subject_id' => fake()->numberBetween(1, 9999),
            'school_class_id' => fake()->numberBetween(1, 9999),
            'class_section_id' => null,
            'staff_id' => null,
            'room_id' => null,
            'day_of_week' => 0,
            'period_number' => 1,
            'starts_at' => '08:00',
            'ends_at' => '08:55',
            'duration_minutes' => 55,
            'entry_type' => ScheduleEntryType::Lesson,
            'status' => ScheduleEntryStatus::Scheduled,
            'is_recurring' => false,
            'notes' => null,
        ];
    }

    public function at(int $day, int $period, string $startsAt = '08:00', string $endsAt = '08:55'): static
    {
        return $this->state(fn (): array => [
            'day_of_week' => $day,
            'period_number' => $period,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);
    }

    public function lesson(): static
    {
        return $this->state(fn (): array => ['entry_type' => ScheduleEntryType::Lesson]);
    }

    public function assessment(): static
    {
        return $this->state(fn (): array => ['entry_type' => ScheduleEntryType::Assessment]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (): array => ['status' => ScheduleEntryStatus::Cancelled]);
    }
}
