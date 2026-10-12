<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Infrastructure\Contracts;

use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyQueryContract;
use App\Domains\Gradebook\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\Gradebook\Domain\References\PolicyReference;

/**
 * AcademicPoliciesContract implementation (SPR-010).
 *
 * Maps the Academic Policies engine onto the Gradebook domain contract.
 * Standing thresholds stay policy-driven — never hardcoded in the engine.
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
