<?php

declare(strict_types=1);

namespace Database\Factories\Timetable;

use App\Domains\Timetable\Domain\Enums\TimetableStatus;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Timetable>
 */
class TimetableFactory extends Factory
{
    protected $model = Timetable::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'name' => 'Emploi du temps '.fake()->year(),
            'code' => fake()->unique()->regexify('TT-[0-9]{4}'),
            'status' => TimetableStatus::Draft,
            'version' => 1,
            'total_entries' => 0,
            'created_by' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['status' => TimetableStatus::Draft]);
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => TimetableStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'status' => TimetableStatus::Archived,
            'archived_at' => now(),
        ]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function forTerm(int $yearId, ?int $termId = null): static
    {
        return $this->state(fn (): array => [
            'academic_calendar_year_id' => $yearId,
            'academic_calendar_term_id' => $termId,
        ]);
    }
}
