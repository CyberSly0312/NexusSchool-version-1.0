<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Contracts;

use App\Domains\Gradebook\Domain\References\PolicyReference;

/**
 * Read-only gateway to the Academic Policies engine (SPR-010).
 *
 * Student-level academic standing thresholds are policy-driven, never
 * hardcoded. The engine reads the per-school key/value policies through this
 * contract and falls back to the configurable engine defaults when the school
 * did not define a policy.
 */
interface AcademicPoliciesContract
{
    /**
     * The active school-scoped policy for a key, when the school defined one.
     */
    public function policy(string $schoolId, string $key): ?PolicyReference;
}
