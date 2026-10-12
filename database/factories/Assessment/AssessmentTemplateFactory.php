<?php

declare(strict_types=1);

namespace Database\Factories\Assessment;

use App\Domains\Assessment\Infrastructure\Models\AssessmentTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentTemplate>
 */
class AssessmentTemplateFactory extends Factory
{
    protected $model = AssessmentTemplate::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'name' => fake()->unique()->words(3, true),
            'assessment_type_id' => AssessmentTypeFactory::new(),
            'description' => fake()->sentence(),
            'default_maximum_score' => 20.0,
            'default_passing_score' => 10.0,
            'default_weight' => 1.0,
            'structure' => null,
            'is_active' => true,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }
}
