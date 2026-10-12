<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Contracts;

use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyQueryContract;
use App\Domains\Assessment\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\Assessment\Domain\References\PolicyReference;

/**
 * AcademicPoliciesContract implementation (SPR-009).
 *
 * Maps the Academic Policies engine onto the Assessment domain contract.
 * Statistics defaults stay policy-driven — never hardcoded in the engine.
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
