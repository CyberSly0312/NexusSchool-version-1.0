<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Application\DTOs;

use App\Domains\ReportCard\Domain\Enums\ReportCardType;

/**
 * Data required to create a report card run (SPR-011).
 */
final readonly class CreateReportCardData
{
    public function __construct(
        public string $schoolId,
        public int|string $yearId,
        public int|string $termId,
        public int|string|null $classId = null,
        public int|string|null $sectionId = null,
        public ReportCardType $reportCardType = ReportCardType::Term,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            yearId: (int) $data['academic_calendar_year_id'],
            termId: (int) $data['academic_calendar_term_id'],
            classId: isset($data['school_class_id']) ? (int) $data['school_class_id'] : null,
            sectionId: isset($data['class_section_id']) ? (int) $data['class_section_id'] : null,
            reportCardType: ReportCardType::tryFrom((string) ($data['report_card_type'] ?? 'term')) ?? ReportCardType::Term,
        );
    }
}
