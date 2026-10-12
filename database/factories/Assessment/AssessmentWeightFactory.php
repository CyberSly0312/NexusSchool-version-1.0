<?php

declare(strict_types=1);

namespace Database\Factories\Assessment;

use App\Domains\Assessment\Infrastructure\Models\AssessmentWeight;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentWeight>
 */
class AssessmentWeightFactory extends Factory
{
    protected $model = AssessmentWeight::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'assessment_type_id' => AssessmentTypeFactory::new(),
            'academic_subject_id' => null,
            'academic_calendar_year_id' => null,
            'academic_calendar_term_id' => null,
            'weight' => fake()->randomFloat(2, 1, 10),
            'is_default' => false,
            'is_active' => true,
            'effective_from' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function default(): static
    {
        return $this->state(fn (): array => [
            'is_default' => true,
            'is_active' => true,
        ]);
    }
}
