<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\References;

/**
 * A published class council decision consumed by the certificate eligibility
 * orchestrator (SPR-015, supplied by SPR-013).
 */
final readonly class CouncilDecisionReference
{
    public function __construct(
        public int|string $decisionId,
        public string $decisionType,
        public string $code,
        public string $label,
    ) {
    }

    public function isPassed(): bool
    {
        return $this->decisionType === 'passed';
    }
}
