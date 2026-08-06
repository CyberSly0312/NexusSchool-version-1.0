<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Infrastructure\Contracts;

use App\Domains\ReportCard\Domain\Contracts\AcademicPoliciesContract;

/**
 * AcademicPoliciesContract implementation (SPR-011).
 *
 * Stub — the Academic Policies engine ships in a later sprint. It documents
 * the intended integration: the Report Card engine will render the policies
 * applicable to the period through this contract and never query the policy
 * models directly.
 */
final class AcademicPoliciesAdapter implements AcademicPoliciesContract
{
    public function forPeriod(string $schoolId, int|string $yearId, int|string $termId): array
    {
        return [];
    }
}
