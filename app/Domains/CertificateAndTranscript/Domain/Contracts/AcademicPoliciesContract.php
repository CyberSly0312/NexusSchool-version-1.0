<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Contracts;

use App\Domains\CertificateAndTranscript\Domain\References\PolicyReference;

/**
 * Read-only gateway to the Academic Policies engine (SPR-015).
 *
 * Certificate eligibility thresholds are policy-driven, never hardcoded. The
 * orchestrator reads the per-school key/value policies through this contract.
 */
interface AcademicPoliciesContract
{
    /**
     * The active school-scoped policy for a key, when the school defined one.
     */
    public function policy(string $schoolId, string $key): ?PolicyReference;
}
