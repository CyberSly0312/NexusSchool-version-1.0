<?php

declare(strict_types=1);

namespace Database\Factories\ReportCard;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;
use App\Domains\ReportCard\Domain\Enums\ReportCardType;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportCard>
 */
class ReportCardFactory extends Factory
{
    protected $model = ReportCard::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'academic_calendar_year_id' => AcademicCalendarYear::factory(),
            'academic_calendar_term_id' => AcademicCalendarTerm::factory(),
            'school_class_id' => SchoolClass::factory(),
            'class_section_id' => null,
            'report_card_type' => ReportCardType::Term,
            'status' => ReportCardStatus::Draft,
            'created_by' => null,
            'generated_at' => null,
            'published_at' => null,
            'archived_at' => null,
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

    public function generated(): static
    {
        return $this->state(fn (): array => [
            'status' => ReportCardStatus::Generated,
            'generated_at' => now(),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => ReportCardStatus::Published,
            'generated_at' => now()->subHour(),
            'published_at' => now(),
        ]);
    }
}
