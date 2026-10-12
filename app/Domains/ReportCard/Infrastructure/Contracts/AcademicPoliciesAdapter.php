<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Infrastructure\Contracts;

use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyQueryContract;
use App\Domains\ReportCard\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\ReportCard\Domain\References\PolicyReference;

/**
 * AcademicPoliciesContract implementation (SPR-011).
 *
 * Maps the Academic Policies engine onto the Report Card domain contract. The
 * Report Card engine renders the policies applicable to the period through this
 * contract and never queries the policy models directly.
 */
final class AcademicPoliciesAdapter implements AcademicPoliciesContract
{
    public function __construct(
        private readonly AcademicPolicyQueryContract $policies,
    ) {
    }

    public function forPeriod(string $schoolId, int|string $yearId, int|string $termId): array
    {
        return array_map(
            static fn ($policy): PolicyReference => new PolicyReference(
                code: $policy->key,
                label: $policy->label,
                description: $policy->description,
            ),
            $this->policies->forPeriod($schoolId, $yearId, $termId),
        );
    }
}
