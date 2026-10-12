<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Infrastructure\Contracts;

use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyQueryContract;
use App\Domains\ClassCouncil\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\ClassCouncil\Domain\References\PolicyReference;

/**
 * AcademicPoliciesContract implementation (SPR-013).
 *
 * Maps the Academic Policies engine onto the Class Council domain contract.
 * The promotion threshold stays policy-driven — never hardcoded in the engine.
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
