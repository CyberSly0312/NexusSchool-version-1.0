<?php

declare(strict_types=1);

namespace Database\Factories\Assessment;

use App\Domains\Assessment\Infrastructure\Models\AssessmentRemark;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentRemark>
 */
class AssessmentRemarkFactory extends Factory
{
    protected $model = AssessmentRemark::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'assessment_id' => AssessmentFactory::new(),
            'assessment_score_id' => null,
            'remark' => fake()->sentence(),
            'remark_type' => 'general',
            'remark_by' => null,
            'remark_at' => now(),
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }
}
