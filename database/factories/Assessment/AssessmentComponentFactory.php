<?php

declare(strict_types=1);

namespace Database\Factories\Assessment;

use App\Domains\Assessment\Infrastructure\Models\AssessmentComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentComponent>
 */
class AssessmentComponentFactory extends Factory
{
    protected $model = AssessmentComponent::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'assessment_id' => AssessmentFactory::new(),
            'name' => fake()->unique()->randomElement([
                'Partie écrite', 'Partie orale', 'Pratique', 'Rapport', 'Dossier',
            ]),
            'description' => fake()->sentence(),
            'maximum_score' => 20.0,
            'weight' => 1.0,
            'sort_order' => fake()->numberBetween(1, 5),
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
}
