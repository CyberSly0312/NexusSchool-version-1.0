<?php

namespace Database\Factories;

use App\Domains\AcademicYear\Enums\AcademicYearStatus;
use App\Domains\School\Models\School;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    public function definition(): array
    {
        $startYear = (int) fake()->year();
        $endYear = $startYear + 1;

        return [
            'school_id' => 1,
            'name' => "{$startYear}-{$endYear}",
            'code' => "AY-{$startYear}-{$endYear}",
            'start_date' => "{$startYear}-09-01",
            'end_date' => "{$endYear}-07-31",
            'description' => fake()->sentence(),
            'status' => AcademicYearStatus::Draft,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => AcademicYearStatus::Active,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => AcademicYearStatus::Draft,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => AcademicYearStatus::Archived,
        ]);
    }

    public function forSchool(School $school): static
    {
        return $this->state(fn () => [
            'school_id' => $school->id,
        ]);
    }
}
