<?php

declare(strict_types=1);

namespace Database\Factories\Timetable;

use App\Domains\Timetable\Domain\Enums\AvailabilityConstraintType;
use App\Domains\Timetable\Infrastructure\Models\AvailabilityConstraint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AvailabilityConstraint>
 */
class AvailabilityConstraintFactory extends Factory
{
    protected $model = AvailabilityConstraint::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'timetable_id' => \App\Domains\Timetable\Infrastructure\Models\Timetable::factory(),
            'constraint_type' => AvailabilityConstraintType::Teacher,
            'target_type' => AvailabilityConstraintType::Teacher->value,
            'target_id' => (string) fake()->unique()->numberBetween(1, 99999),
            'day_of_week' => fake()->numberBetween(0, 6),
            'starts_at' => null,
            'ends_at' => null,
            'reason' => null,
        ];
    }

    public function forTeacher(int $teacherId): static
    {
        return $this->state(fn (): array => [
            'constraint_type' => AvailabilityConstraintType::Teacher,
            'target_type' => AvailabilityConstraintType::Teacher->value,
            'target_id' => (string) $teacherId,
        ]);
    }
}
