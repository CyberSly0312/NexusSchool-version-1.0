<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Infrastructure\Contracts;

use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyQueryContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\CertificateAndTranscript\Domain\References\PolicyReference;

/**
 * AcademicPoliciesContract implementation (SPR-015).
 *
 * Maps the Academic Policies engine onto the Certificate & Transcript domain
 * contract. Eligibility thresholds stay policy-driven — never hardcoded in the
 * orchestrator (ADR-021 Decision 4).
 */
final class AcademicPoliciesAdapter implements AcademicPoliciesContract
{
    public function __construct(
        private readonly AcademicPolicyQueryContract $policies,
    ) {
    }

    public function policy(string $schoolId, string $key): ?PolicyReference
    {
        $policy = $this->policies->policy($schoolId, $key);

        if ($policy === null) {
            return null;
        }

        return new PolicyReference(
            key: $policy->key,
            label: $policy->label,
            value: $policy->value,
            valueType: $policy->valueType->value,
            description: $policy->description,
        );
    }
}
