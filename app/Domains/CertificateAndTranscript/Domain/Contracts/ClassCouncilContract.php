<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Contracts;

use App\Domains\CertificateAndTranscript\Domain\References\CouncilDecisionReference;

/**
 * Read-only gateway to the Class Council engine (SPR-015).
 *
 * Only *published* council sessions are visible to other engines (ADR-013). The
 * certificate eligibility orchestrator consumes the published council decision
 * covering the student's final period — the academic truth the platform must
 * respect.
 */
interface ClassCouncilContract
{
    /**
     * The published council decision of one student's latest session covering
     * the given period, when one exists.
     */
    public function publishedDecision(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
    ): ?CouncilDecisionReference;
}
