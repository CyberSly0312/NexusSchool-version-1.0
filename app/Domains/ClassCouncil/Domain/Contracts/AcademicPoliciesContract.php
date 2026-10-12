<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\Contracts;

use App\Domains\ClassCouncil\Domain\References\PolicyReference;

/**
 * Read-only gateway to the Academic Policies engine (SPR-013).
 *
 * The promotion passing threshold is policy-driven, never hardcoded. The engine
 * reads the per-school key/value policies through this contract and falls back
 * to the configurable engine default when the school did not define a policy.
 */
interface AcademicPoliciesContract
{
    /**
     * The active school-scoped policy for a key, when the school defined one.
     */
    public function policy(string $schoolId, string $key): ?PolicyReference;
}
