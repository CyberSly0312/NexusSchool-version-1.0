<?php

declare(strict_types=1);

namespace Database\Factories\ClassCouncil;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CouncilSession>
 */
class CouncilSessionFactory extends Factory
{
    protected $model = CouncilSession::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'academic_calendar_year_id' => AcademicCalendarYear::factory(),
            'academic_calendar_term_id' => AcademicCalendarTerm::factory(),
            'school_class_id' => SchoolClass::factory(),
            'class_section_id' => null,
            'reference' => 'CC-'.Str::upper(Str::random(8)),
            'status' => CouncilSessionStatus::Draft,
            'held_at' => null,
            'published_at' => null,
            'archived_at' => null,
            'created_by' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function forClass(int|string $classId): static
    {
        return $this->state(fn (): array => ['school_class_id' => $classId]);
    }

    public function convened(): static
    {
        return $this->state(fn (): array => [
            'status' => CouncilSessionStatus::Convened,
            'held_at' => now(),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => CouncilSessionStatus::Published,
            'held_at' => now()->subHour(),
            'published_at' => now(),
        ]);
    }
}
