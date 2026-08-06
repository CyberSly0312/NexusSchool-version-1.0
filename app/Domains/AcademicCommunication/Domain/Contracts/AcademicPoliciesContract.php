<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Contracts;

use App\Domains\AcademicCommunication\Domain\References\AcademicPolicyReference;

/**
 * Read-only gateway to the Academic Policies engine (SPR-012). Policies are
 * validated by their owning engine and consumed here to reflect the school's
 * official rules (grading scale, honor thresholds, …) in rendered documents.
 */
interface AcademicPoliciesContract
{
    /**
     * @param  list<string>  $keys
     *
     * @return list<AcademicPolicyReference>
     */
    public function policies(string $schoolId, array $keys = []): array;

    public function policy(string $schoolId, string $key): ?AcademicPolicyReference;
}
