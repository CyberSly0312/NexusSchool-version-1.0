<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Domain\Contracts;

use App\Domains\Assessment\Domain\References\PolicyReference;

/**
 * Read-only gateway to the Academic Policies engine (SPR-009).
 *
 * The statistics default passing threshold is policy-driven per school, never
 * hardcoded. The engine reads the per-school key/value policies through this
 * contract and falls back to the configurable engine default when the school
 * did not define a policy. An explicit assessment passing score always
 * precedes any policy or configuration default.
 */
interface AcademicPoliciesContract
{
    /**
     * The active school-scoped policy for a key, when the school defined one.
     */
    public function policy(string $schoolId, string $key): ?PolicyReference;
}
