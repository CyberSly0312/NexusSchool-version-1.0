<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\DTOs;

use App\Domains\Enrollment\Domain\Enums\PromotionDecision;
use App\Domains\Enrollment\Domain\ValueObjects\AcademicResult;

final readonly class PromotionData
{
    public function __construct(
        public PromotionDecision $decision,
        public int $academicCalendarYearId,
        public string $promotedOn,
        public ?int $toClassId = null,
        public ?int $toSectionId = null,
        public ?AcademicResult $result = null,
        public ?string $notes = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            decision: PromotionDecision::from((string) $data['decision']),
            academicCalendarYearId: (int) $data['academic_calendar_year_id'],
            promotedOn: (string) $data['promoted_on'],
            toClassId: isset($data['to_class_id']) ? (int) $data['to_class_id'] : null,
            toSectionId: isset($data['to_section_id']) ? (int) $data['to_section_id'] : null,
            result: isset($data['result']) ? AcademicResult::fromArray($data['result']) : null,
            notes: $data['notes'] ?? null,
        );
    }
}
