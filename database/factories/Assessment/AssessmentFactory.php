<?php

declare(strict_types=1);

namespace Database\Factories\Assessment;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Assessment\Domain\Enums\AssessmentStatus;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use Database\Factories\Staff\StaffFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assessment>
 */
class AssessmentFactory extends Factory
{
    protected $model = Assessment::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'academic_calendar_year_id' => AcademicCalendarYear::factory(),
            'academic_calendar_term_id' => AcademicCalendarTerm::factory(),
            'academic_subject_id' => AcademicSubject::factory(),
            'school_class_id' => SchoolClass::factory(),
            'class_section_id' => null,
            'staff_id' => StaffFactory::new(),
            'assessment_type_id' => AssessmentTypeFactory::new(),
            'assessment_template_id' => null,
            'assessment_session_id' => null,
            'title' => fake()->unique()->words(4, true),
            'description' => fake()->sentence(),
            'maximum_score' => 20.0,
            'passing_score' => 10.0,
            'weight' => 1.0,
            'status' => AssessmentStatus::Draft,
            'scheduled_at' => null,
            'published_at' => null,
            'completed_at' => null,
            'archived_at' => null,
            'created_by' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (): array => [
            'status' => AssessmentStatus::Scheduled,
            'scheduled_at' => now(),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => AssessmentStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => AssessmentStatus::Completed,
            'published_at' => now()->subWeek(),
            'completed_at' => now(),
        ]);
    }
}
