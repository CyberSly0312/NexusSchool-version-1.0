<?php

declare(strict_types=1);

namespace Database\Factories\Enrollment;

use App\Domains\Enrollment\Domain\Enums\GraduationDecision;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentGraduation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EnrollmentGraduation>
 */
class EnrollmentGraduationFactory extends Factory
{
    protected $model = EnrollmentGraduation::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'enrollment_id' => \App\Domains\Enrollment\Infrastructure\Models\Enrollment::factory(),
            'student_id' => \App\Domains\Student\Infrastructure\Models\Student::factory(),
            'education_level_id' => \Database\Factories\Academic\EducationLevelFactory::new()
                ->state(['school_id' => 'acad-'.(string) \Illuminate\Support\Str::ulid()]),
            'decision' => GraduationDecision::Graduated,
            'graduated_on' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'certificate_reference' => fake()->optional()->bothify('CERT-####'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
