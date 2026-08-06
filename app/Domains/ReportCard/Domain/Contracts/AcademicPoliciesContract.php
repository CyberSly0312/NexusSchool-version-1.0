<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\Contracts;

use App\Domains\ReportCard\Domain\References\PolicyReference;

/**
 * Read-only gateway to the Academic Policies engine (SPR-011).
 *
 * The policy adapter is a stub returning an empty list — the Academic Policies
 * engine ships in a later sprint. The Report Card engine integrates through
 * this contract only, so the stub can be replaced without touching the domain.
 */
interface AcademicPoliciesContract
{
    /**
     * Policies applicable to a period, in rendering order.
     *
     * @return list<PolicyReference>
     */
    public function forPeriod(string $schoolId, int|string $yearId, int|string $termId): array;
}
