<?php

declare(strict_types=1);

namespace Database\Factories\Enrollment;

use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Enums\EnrollmentType;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        // Academic rows are school-scoped with unique indexes, so each nested
        // academic context gets its own fictional school id. Foreign keys are
        // not enforced in tests (DB_FOREIGN_KEYS=0) and callers always pass the
        // real enrollment school_id explicitly.
        $academicSchoolId = 'acad-'.(string) \Illuminate\Support\Str::ulid();

        return [
            'school_id' => null,
            'student_id' => \App\Domains\Student\Infrastructure\Models\Student::factory(),
            'academic_calendar_year_id' => \Database\Factories\Academic\AcademicCalendarYearFactory::new()
                ->active()
                ->state(['school_id' => $academicSchoolId]),
            'academic_program_id' => \Database\Factories\Academic\AcademicProgramFactory::new()
                ->state(['school_id' => $academicSchoolId]),
            'education_level_id' => \Database\Factories\Academic\EducationLevelFactory::new()
                ->state(['school_id' => $academicSchoolId]),
            'school_class_id' => \Database\Factories\Academic\SchoolClassFactory::new()
                ->state([
                    'school_id' => $academicSchoolId,
                    'academic_program_id' => \Database\Factories\Academic\AcademicProgramFactory::new()
                        ->state([
                            'school_id' => $academicSchoolId,
                            'name' => 'Programme '.\Illuminate\Support\Str::random(6),
                        ]),
                ]),
            'class_section_id' => null,
            'enrollment_number' => fake()->unique()->regexify('ENR-[A-Z0-9]{6}-[0-9]{4}'),
            'enrollment_type' => EnrollmentType::New,
            'status' => EnrollmentStatus::Active,
            'enrolled_at' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => ['status' => EnrollmentStatus::Active]);
    }

    public function suspended(): static
    {
        return $this->state(fn (): array => [
            'status' => EnrollmentStatus::Suspended,
            'suspension_reason' => fake()->sentence(),
        ]);
    }

    public function withdrawn(): static
    {
        return $this->state(fn (): array => [
            'status' => EnrollmentStatus::Withdrawn,
            'exited_at' => now()->toDateString(),
            'exit_reason' => fake()->sentence(),
        ]);
    }

    public function graduated(): static
    {
        return $this->state(fn (): array => [
            'status' => EnrollmentStatus::Graduated,
            'exited_at' => now()->toDateString(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'status' => EnrollmentStatus::Archived,
            'exited_at' => now()->toDateString(),
            'deleted_at' => now(),
        ]);
    }
}
