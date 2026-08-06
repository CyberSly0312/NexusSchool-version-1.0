<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\References;

/**
 * A class council decision rendered on the bulletin (SPR-011). Supplied by the
 * Class Council engine through the contract.
 */
final readonly class CouncilDecisionReference
{
    public function __construct(
        public string $code,
        public string $label,
        public ?string $decision = null,
    ) {
    }
}
