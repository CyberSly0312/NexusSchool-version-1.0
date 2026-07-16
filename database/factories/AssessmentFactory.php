<?php

namespace Database\Factories;

use App\Models\AcademicClass;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Domains\School\Models\School;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentFactory extends Factory
{
    protected $model = Assessment::class;

    public function definition(): array
    {
        return [
            'school_id' => '1',
            'academic_year_id' => AcademicYear::factory(),
            'academic_term_id' => AcademicTerm::factory(),
            'user_id' => User::factory(),
            'subject_id' => fn(array $attrs) => Subject::factory()->create(['school_id' => $attrs['school_id'] ?? '1']),
            'academic_class_id' => AcademicClass::factory(),
            'assessment_code' => strtoupper(fake()->unique()->bothify('ASS-##??')),
            'title' => fake()->words(3, true),
            'assessment_type' => fake()->randomElement(['exam', 'quiz', 'assignment', 'project', 'midterm', 'final']),
            'assessment_category' => fake()->randomElement(['formative', 'summative']),
            'maximum_score' => 100,
            'passing_score' => 50,
            'weight' => fake()->randomFloat(2, 1, 5),
            'assessment_date' => fake()->date(),
            'publish_date' => fake()->optional()->date(),
            'submission_deadline' => fake()->optional()->date(),
            'status' => 'draft',
            'is_published' => false,
            'is_locked' => false,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function forSchool(mixed $school): static
    {
        return $this->state(fn() => [
            'school_id' => $school instanceof School ? $school->id : $school,
        ]);
    }
}
