<?php

declare(strict_types=1);

namespace Database\Factories\Timetable;

use App\Domains\Timetable\Domain\Enums\ConflictType;
use App\Domains\Timetable\Infrastructure\Models\TimetableConflict;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimetableConflict>
 */
class TimetableConflictFactory extends Factory
{
    protected $model = TimetableConflict::class;

    public function forSchool(string $schoolId): static
    {
        return $this->state(['school_id' => $schoolId]);
    }

    public function definition(): array
    {
        return [
            'school_id' => null,
            'timetable_id' => \App\Domains\Timetable\Infrastructure\Models\Timetable::factory(),
            'detection_id' => \Illuminate\Support\Str::uuid()->toString(),
            'entry_id' => null,
            'conflict_type' => ConflictType::TeacherConflict,
            'day_of_week' => 0,
            'resolved_at' => null,
            'resolution' => null,
        ];
    }
}
