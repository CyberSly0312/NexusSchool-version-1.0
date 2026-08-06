<?php

declare(strict_types=1);

namespace Database\Factories\Staff;

use App\Domains\Staff\Domain\Enums\AssignmentType;
use App\Domains\Staff\Infrastructure\Models\Assignment;
use App\Domains\Staff\Infrastructure\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'staff_id' => Staff::factory(),
            'employment_id' => null,
            'assignment_type' => AssignmentType::Department,
            'department_id' => null,
            'position_id' => null,
            'campus_school_id' => null,
            'assignment_start_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'assignment_end_date' => null,
            'is_current' => true,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function department(): static
    {
        return $this->state(fn (): array => ['assignment_type' => AssignmentType::Department]);
    }

    public function position(): static
    {
        return $this->state(fn (): array => ['assignment_type' => AssignmentType::Position]);
    }

    public function forStaff(int $staffId): static
    {
        return $this->state(fn (): array => ['staff_id' => $staffId]);
    }

    public function forEmployment(int $employmentId): static
    {
        return $this->state(fn (): array => ['employment_id' => $employmentId]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }
}
