<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Application\DTOs;

use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionCategory;
use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType;

/**
 * Input of the council decision action (SPR-013).
 *
 * A decision is taken for one student (studentId) or for the whole class when
 * no student is given. An override of the automatic decision requires a
 * justification and an approval (the acting user must be supplied).
 */
final readonly class RecordCouncilDecisionData
{
    public function __construct(
        public string $schoolId,
        public int|string $sessionId,
        public CouncilDecisionType $decisionType,
        public CouncilDecisionCategory $category,
        public string $label,
        public int|string|null $studentId = null,
        public ?string $summary = null,
        public ?string $recommendation = null,
        public ?string $decisionDate = null,
        public bool $isOverride = false,
        public ?string $justification = null,
        public int|string|null $approvedBy = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            sessionId: (int) $data['council_session_id'],
            decisionType: CouncilDecisionType::tryFrom((string) ($data['decision_type'] ?? 'reported')) ?? CouncilDecisionType::Reported,
            category: CouncilDecisionCategory::tryFrom((string) ($data['category'] ?? 'promotion')) ?? CouncilDecisionCategory::Promotion,
            label: (string) ($data['label'] ?? ''),
            studentId: isset($data['student_id']) ? (int) $data['student_id'] : null,
            summary: isset($data['summary']) ? (string) $data['summary'] : null,
            recommendation: isset($data['recommendation']) ? (string) $data['recommendation'] : null,
            decisionDate: isset($data['decision_date']) ? (string) $data['decision_date'] : null,
            isOverride: (bool) ($data['is_override'] ?? false),
            justification: isset($data['justification']) ? (string) $data['justification'] : null,
            approvedBy: isset($data['approved_by']) ? (int) $data['approved_by'] : null,
        );
    }
}
