<?php

namespace Database\Factories;

use App\Domains\Gradebook\Enums\AssessmentResultStatus;
use App\Domains\Gradebook\Enums\LetterGrade;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Domains\School\Models\School;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentResultFactory extends Factory
{
    protected $model = AssessmentResult::class;

    public function definition(): array
    {
        $maximumScore = 100;
        $rawScore = fake()->numberBetween(0, $maximumScore);
        $percentage = $rawScore > 0 ? round(($rawScore / $maximumScore) * 100, 2) : 0;
        $letterGrade = LetterGrade::fromPercentage($percentage);
        $coefficient = fake()->randomFloat(2, 1, 3);

        return [
            'school_id' => School::factory(),
            'student_id' => Student::factory(),
            'student_enrollment_id' => null,
            'assessment_id' => Assessment::factory(),
            'subject_id' => fn(array $attrs) => Assessment::find($attrs['assessment_id'])?->subject_id ?? Subject::factory(),
            'user_id' => null,
            'academic_year_id' => fn(array $attrs) => Assessment::find($attrs['assessment_id'])?->academic_year_id ?? AcademicYear::factory(),
            'academic_term_id' => fn(array $attrs) => Assessment::find($attrs['assessment_id'])?->academic_term_id ?? AcademicTerm::factory(),
            'raw_score' => $rawScore,
            'adjusted_score' => null,
            'maximum_score' => $maximumScore,
            'percentage' => $percentage,
            'letter_grade' => $letterGrade->value,
            'grade_point' => LetterGrade::gradePointFromPercentage($percentage),
            'coefficient' => $coefficient,
            'weighted_score' => round($rawScore * $coefficient, 4),
            'rank_in_assessment' => null,
            'is_absent' => false,
            'is_excused' => false,
            'is_published' => false,
            'is_locked' => false,
            'status' => AssessmentResultStatus::Draft->value,
            'remarks' => null,
        ];
    }

    public function forSchool(mixed $school): static
    {
        return $this->state(fn() => [
            'school_id' => $school instanceof School ? $school->id : $school,
        ]);
    }

    public function absent(): static
    {
        return $this->state(fn() => [
            'raw_score' => 0,
            'adjusted_score' => null,
            'percentage' => 0,
            'letter_grade' => null,
            'grade_point' => null,
            'weighted_score' => null,
            'is_absent' => true,
        ]);
    }

    public function status(AssessmentResultStatus $status): static
    {
        return $this->state(fn() => [
            'status' => $status->value,
            'is_published' => $status === AssessmentResultStatus::Published,
            'is_locked' => $status === AssessmentResultStatus::Locked,
        ]);
    }
}
