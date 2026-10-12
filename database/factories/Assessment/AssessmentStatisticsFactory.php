<?php

declare(strict_types=1);

namespace Database\Factories\Assessment;

use App\Domains\Assessment\Infrastructure\Models\AssessmentStatistics;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentStatistics>
 */
class AssessmentStatisticsFactory extends Factory
{
    protected $model = AssessmentStatistics::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'assessment_id' => AssessmentFactory::new(),
            'total_scores' => fake()->numberBetween(10, 50),
            'average' => fake()->randomFloat(2, 0, 20),
            'minimum' => fake()->randomFloat(2, 0, 8),
            'maximum' => fake()->randomFloat(2, 12, 20),
            'median' => fake()->randomFloat(2, 5, 15),
            'pass_rate' => fake()->randomFloat(2, 0, 100),
            'completion_rate' => fake()->randomFloat(2, 50, 100),
            'generated_by' => null,
            'generated_at' => now(),
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }
}
