<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Contracts;

use App\Domains\AcademicCommunication\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\AcademicCommunication\Domain\References\AcademicPolicyReference;
use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyQueryContract;

/**
 * AcademicPoliciesContract implementation (SPR-012).
 *
 * Maps the Academic Policies engine onto the Academic Communication domain
 * contract. Policies are validated by their owning engine and consumed here to
 * reflect the school's official rules (grading scale, honor thresholds, …) in
 * rendered documents.
 */
final class AcademicPoliciesAdapter implements AcademicPoliciesContract
{
    public function __construct(
        private readonly AcademicPolicyQueryContract $policies,
    ) {
    }

    public function policies(string $schoolId, array $keys = []): array
    {
        return array_map(
            static fn ($policy): AcademicPolicyReference => new AcademicPolicyReference(
                key: $policy->key,
                label: $policy->label,
                value: $policy->value,
                scope: $policy->scope->value,
            ),
            $this->policies->policies($schoolId, $keys),
        );
    }

    public function policy(string $schoolId, string $key): ?AcademicPolicyReference
    {
        $policy = $this->policies->policy($schoolId, $key);

        if ($policy === null) {
            return null;
        }

        return new AcademicPolicyReference(
            key: $policy->key,
            label: $policy->label,
            value: $policy->value,
            scope: $policy->scope->value,
        );
    }
}
