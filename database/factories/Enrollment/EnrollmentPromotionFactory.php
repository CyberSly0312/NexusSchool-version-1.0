<?php

declare(strict_types=1);

namespace Database\Factories\Enrollment;

use App\Domains\Enrollment\Domain\Enums\PromotionDecision;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentPromotion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EnrollmentPromotion>
 */
class EnrollmentPromotionFactory extends Factory
{
    protected $model = EnrollmentPromotion::class;

    public function definition(): array
    {
        $academicSchoolId = 'acad-'.(string) \Illuminate\Support\Str::ulid();

        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'enrollment_id' => \App\Domains\Enrollment\Infrastructure\Models\Enrollment::factory(),
            'student_id' => \App\Domains\Student\Infrastructure\Models\Student::factory(),
            'academic_calendar_year_id' => \Database\Factories\Academic\AcademicCalendarYearFactory::new()
                ->active()
                ->state(['school_id' => $academicSchoolId]),
            'from_class_id' => \Database\Factories\Academic\SchoolClassFactory::new()->state([
                'school_id' => $academicSchoolId,
                'academic_program_id' => \Database\Factories\Academic\AcademicProgramFactory::new()
                    ->state(['school_id' => $academicSchoolId]),
            ]),
            'to_class_id' => null,
            'decision' => PromotionDecision::Promoted,
            'promoted_on' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'academic_result' => [
                'average' => fake()->randomFloat(2, 8, 20),
                'rank' => fake()->optional()->numberBetween(1, 40),
            ],
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
