<?php

namespace Database\Factories;

use App\Domains\AcademicTerm\Enums\AcademicTermStatus;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicTerm>
 */
class AcademicTermFactory extends Factory
{
    protected $model = AcademicTerm::class;

    public function definition(): array
    {
        $startYear = (int) fake()->year();
        $endYear = $startYear + 1;
        $uniqueCode = strtoupper(fake()->bothify('??-###'));

        return [
            'academic_year_id' => AcademicYear::factory(),
            'school_id' => 1,
            'name' => 'Term-'.$uniqueCode,
            'code' => $uniqueCode,
            'start_date' => "{$startYear}-09-01",
            'end_date' => "{$startYear}-12-31",
            'order' => 1,
            'description' => fake()->sentence(),
            'status' => AcademicTermStatus::Draft,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => AcademicTermStatus::Active,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => AcademicTermStatus::Draft,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => AcademicTermStatus::Archived,
        ]);
    }

    public function forSchool(mixed $school): static
    {
        return $this->state(fn () => [
            'school_id' => $school instanceof \App\Domains\School\Models\School ? $school->id : $school,
        ]);
    }

    public function forAcademicYear(AcademicYear $academicYear): static
    {
        return $this->state(fn () => [
            'academic_year_id' => $academicYear->id,
            'school_id' => $academicYear->school_id,
            'start_date' => $academicYear->start_date->format('Y-m-d'),
            'end_date' => $academicYear->end_date->format('Y-m-d'),
        ]);
    }
}
