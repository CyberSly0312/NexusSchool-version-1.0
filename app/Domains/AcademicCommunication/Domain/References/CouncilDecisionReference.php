<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\References;

/**
 * One published council decision consumed from the Class Council integration
 * (SPR-012). Only published decisions are ever exposed by the contract;
 * confidential deliberations stay inside the council domain.
 */
final readonly class CouncilDecisionReference
{
    public function __construct(
        public int|string $id,
        public string $summary,
        public string $category,
        public int|string|null $studentId = null,
        public ?string $decisionDate = null,
        public ?string $recommendation = null,
    ) {
    }
}
