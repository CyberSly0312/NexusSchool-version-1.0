<?php

namespace Database\Factories;

use App\Domains\StudentEnrollment\Enums\EnrollmentStatus;
use App\Domains\StudentEnrollment\Enums\EnrollmentType;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\GradeLevel;
use App\Models\Program;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentEnrollment>
 */
class StudentEnrollmentFactory extends Factory
{
    protected $model = StudentEnrollment::class;

    public function definition(): array
    {
        $enrollmentNumber = strtoupper(fake()->bothify('ENR-####'));
        $enrollmentDate = fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d');

        return [
            'school_id' => '1',
            'student_id' => Student::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'academic_class_id' => AcademicClass::factory(),
            'program_id' => Program::factory(),
            'grade_level_id' => GradeLevel::factory(),
            'section_id' => Section::factory(),
            'classroom_id' => null,
            'academic_term_id' => null,
            'enrollment_number' => $enrollmentNumber,
            'enrollment_date' => $enrollmentDate,
            'effective_date' => $enrollmentDate,
            'enrollment_type' => fake()->randomElement(EnrollmentType::cases())->value,
            'status' => EnrollmentStatus::Pending,
            'previous_school' => null,
            'transfer_reason' => null,
            'exit_date' => null,
            'exit_reason' => null,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => EnrollmentStatus::Pending,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => EnrollmentStatus::Active,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => [
            'status' => EnrollmentStatus::Suspended,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => EnrollmentStatus::Completed,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => EnrollmentStatus::Cancelled,
        ]);
    }

    public function withdrawn(): static
    {
        return $this->state(fn () => [
            'status' => EnrollmentStatus::Withdrawn,
        ]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => [
            'school_id' => $schoolId,
        ]);
    }

    public function forStudent(int $studentId): static
    {
        return $this->state(fn () => [
            'student_id' => $studentId,
        ]);
    }

    public function forAcademicYear(int $academicYearId): static
    {
        return $this->state(fn () => [
            'academic_year_id' => $academicYearId,
        ]);
    }
}
