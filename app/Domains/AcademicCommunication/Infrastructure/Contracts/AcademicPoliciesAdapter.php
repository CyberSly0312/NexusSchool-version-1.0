<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Contracts;

use App\Domains\AcademicCommunication\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\AcademicCommunication\Domain\References\AcademicPolicyReference;

/**
 * AcademicPoliciesContract implementation (SPR-012).
 *
 * The Academic Policies engine is the subject of a later sprint: while it does
 * not exist yet, this adapter answers the contract with empty collections. When
 * the engine lands, only this adapter changes.
 */
final class AcademicPoliciesAdapter implements AcademicPoliciesContract
{
    public function policies(string $schoolId, array $keys = []): array
    {
        return [];
    }

    public function policy(string $schoolId, string $key): ?AcademicPolicyReference
    {
        return null;
    }
}
