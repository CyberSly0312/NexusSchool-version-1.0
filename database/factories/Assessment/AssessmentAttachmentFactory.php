<?php

declare(strict_types=1);

namespace Database\Factories\Assessment;

use App\Domains\Assessment\Infrastructure\Models\AssessmentAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentAttachment>
 */
class AssessmentAttachmentFactory extends Factory
{
    protected $model = AssessmentAttachment::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'assessment_id' => AssessmentFactory::new(),
            'file_name' => fake()->words(3, true).'.pdf',
            'file_path' => 'assessments/'.fake()->uuid().'.pdf',
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(1000, 5000000),
            'uploaded_by' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }
}
