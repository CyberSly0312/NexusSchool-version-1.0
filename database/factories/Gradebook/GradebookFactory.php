<?php

declare(strict_types=1);

namespace Database\Factories\Gradebook;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Gradebook\Domain\Enums\GradebookStatus;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use Database\Factories\Staff\StaffFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gradebook>
 */
class GradebookFactory extends Factory
{
    protected $model = Gradebook::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'academic_calendar_year_id' => AcademicCalendarYear::factory(),
            'academic_calendar_term_id' => AcademicCalendarTerm::factory(),
            'academic_subject_id' => AcademicSubject::factory(),
            'school_class_id' => SchoolClass::factory(),
            'class_section_id' => null,
            'staff_id' => StaffFactory::new(),
            'coefficient' => fake()->randomElement([1, 1.5, 2, 3]),
            'status' => GradebookStatus::Draft,
            'calculated_at' => null,
            'locked_at' => null,
            'published_at' => null,
            'archived_at' => null,
            'created_by' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function forSubject(int|string $subjectId): static
    {
        return $this->state(fn (): array => ['academic_subject_id' => $subjectId]);
    }

    public function calculated(): static
    {
        return $this->state(fn (): array => [
            'status' => GradebookStatus::Calculated,
            'calculated_at' => now(),
        ]);
    }

    public function locked(): static
    {
        return $this->state(fn (): array => [
            'status' => GradebookStatus::Locked,
            'calculated_at' => now()->subHour(),
            'locked_at' => now(),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => GradebookStatus::Published,
            'calculated_at' => now()->subHour(),
            'locked_at' => now()->subMinutes(30),
            'published_at' => now(),
        ]);
    }
}
