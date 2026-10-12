<?php

declare(strict_types=1);

namespace Database\Factories\Assessment;

use App\Domains\Assessment\Domain\Enums\ScoreStatus;
use App\Domains\Assessment\Domain\Enums\ScoreValidationStatus;
use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentScore>
 */
class AssessmentScoreFactory extends Factory
{
    protected $model = AssessmentScore::class;

    public function definition(): array
    {
        $maximum = 20.0;
        $score = fake()->randomFloat(2, 0, 20);

        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'assessment_id' => AssessmentFactory::new(),
            'assessment_component_id' => null,
            'enrollment_id' => Enrollment::factory(),
            'score' => $score,
            'percentage' => round(($score / max($maximum, 1)) * 100, 2),
            'status' => ScoreStatus::Draft,
            'validation_status' => ScoreValidationStatus::Valid,
            'feedback' => fake()->optional()->sentence(),
            'graded_by' => null,
            'graded_at' => now(),
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function forAssessment(int|string $assessmentId): static
    {
        return $this->state(fn (): array => ['assessment_id' => $assessmentId]);
    }

    public function published(): static
    {
        return $this->state(fn (): array => ['status' => ScoreStatus::Published]);
    }

    public function belowPassing(): static
    {
        return $this->state(fn (): array => [
            'score' => fake()->randomFloat(2, 0, 9.99),
            'validation_status' => ScoreValidationStatus::BelowPassing,
        ]);
    }

    public function invalid(): static
    {
        return $this->state(fn (): array => [
            'score' => 25.0,
            'validation_status' => ScoreValidationStatus::AboveMaximum,
        ]);
    }
}
