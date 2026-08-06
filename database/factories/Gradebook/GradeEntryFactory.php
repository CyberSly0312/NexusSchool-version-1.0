<?php

declare(strict_types=1);

namespace Database\Factories\Gradebook;

use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Gradebook\Domain\Enums\GradeEntryStatus;
use App\Domains\Gradebook\Domain\Enums\LetterGrade;
use App\Domains\Gradebook\Infrastructure\Models\GradeEntry;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradeEntry>
 */
class GradeEntryFactory extends Factory
{
    protected $model = GradeEntry::class;

    public function definition(): array
    {
        $percentage = fake()->randomFloat(2, 10, 98);
        $letter = LetterGrade::fromPercentage($percentage);

        return [
            'school_id' => null,
            'gradebook_id' => GradebookFactory::new(),
            'enrollment_id' => Enrollment::factory(),
            'student_id' => Student::factory(),
            'percentage' => $percentage,
            'average' => round($percentage / 5, 2),
            'letter_grade' => $letter->value,
            'grade_point' => $letter->gradePoint(),
            'appreciation' => $letter->appreciation(),
            'rank' => null,
            'rank_denominator' => null,
            'status' => GradeEntryStatus::Draft,
            'remarks' => fake()->optional()->sentence(),
            'graded_by' => null,
            'calculated_at' => now(),
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function forGradebook(int|string $gradebookId): static
    {
        return $this->state(fn (): array => ['gradebook_id' => $gradebookId]);
    }

    public function forStudent(int|string $studentId, int|string $enrollmentId): static
    {
        return $this->state(fn (): array => [
            'student_id' => $studentId,
            'enrollment_id' => $enrollmentId,
        ]);
    }

    public function validated(): static
    {
        return $this->state(fn (): array => ['status' => GradeEntryStatus::Validated]);
    }

    public function locked(): static
    {
        return $this->state(fn (): array => ['status' => GradeEntryStatus::Locked]);
    }
}
