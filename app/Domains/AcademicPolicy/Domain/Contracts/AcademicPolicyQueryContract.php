<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Domain\Contracts;

use App\Domains\AcademicPolicy\Domain\References\AcademicPolicyReference;

/**
 * Read-only gateway to the Academic Policies engine (SPR-009.5).
 *
 * The engine is the single origin of academic rules (ARCHITECTURE_DECISIONS
 * §6). Downstream domains depend on this contract only and never query the
 * policy models directly. Policies are school-scoped: every lookup is bound to
 * the school id and a policy from one school can never leak to another.
 */
interface AcademicPolicyQueryContract
{
    /**
     * Active policies of a school, optionally filtered by keys.
     *
     * @param  list<string>  $keys
     *
     * @return list<AcademicPolicyReference>
     */
    public function policies(string $schoolId, array $keys = []): array;

    /**
     * The active school-scoped policy for a key, when the school defined one.
     */
    public function policy(string $schoolId, string $key): ?AcademicPolicyReference;

    /**
     * Policies applicable to a period of a school, in stable key order.
     *
     * Returns the active school-scoped policies plus the active academic-year
     * scoped policies of the given year. The term id is accepted for forward
     * compatibility; no term-scoped policy exists yet, so it does not filter.
     *
     * @return list<AcademicPolicyReference>
     */
    public function forPeriod(string $schoolId, int|string $yearId, int|string $termId): array;
}
