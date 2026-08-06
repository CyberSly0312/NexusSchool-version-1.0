<?php

declare(strict_types=1);

namespace Database\Factories\Staff;

use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Infrastructure\Models\TeacherProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeacherProfile>
 */
class TeacherProfileFactory extends Factory
{
    protected $model = TeacherProfile::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'staff_id' => Staff::factory(),
            'teaching_license_number' => fake()->optional()->numerify('LIC-########'),
            'teaching_license_valid_until' => fake()->optional()->dateTimeBetween('now', '+5 years')->format('Y-m-d'),
            'is_homeroom_teacher' => fake()->boolean(25),
            'max_weekly_hours' => fake()->optional()->numberBetween(10, 40),
            'preferred_teaching_levels' => fake()->optional()->randomElements(['CP', 'CE1', 'CE2', 'CM1', 'CM2']),
            'teaching_subject_ids' => fake()->optional()->randomElements([1, 2, 3, 4, 5]),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function homeroom(): static
    {
        return $this->state(fn (): array => ['is_homeroom_teacher' => true]);
    }

    public function forStaff(int $staffId): static
    {
        return $this->state(fn (): array => ['staff_id' => $staffId]);
    }
}
