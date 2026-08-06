<?php

declare(strict_types=1);

namespace Database\Factories\ReportCard;

use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\ReportCard\Domain\Enums\ReportCardEntryStatus;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Domains\ReportCard\Infrastructure\Models\ReportCardEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportCardEntry>
 */
class ReportCardEntryFactory extends Factory
{
    protected $model = ReportCardEntry::class;

    public function definition(): array
    {
        return [
            'school_id' => null,
            'report_card_id' => ReportCard::factory(),
            'enrollment_id' => Enrollment::factory(),
            'student_id' => 1,
            'content' => [],
            'academic_document_id' => null,
            'academic_document_number' => null,
            'status' => ReportCardEntryStatus::Pending,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function forReportCard(int|string $reportCardId): static
    {
        return $this->state(fn (): array => ['report_card_id' => $reportCardId]);
    }

    public function included(): static
    {
        return $this->state(fn (): array => ['status' => ReportCardEntryStatus::Included]);
    }
}
