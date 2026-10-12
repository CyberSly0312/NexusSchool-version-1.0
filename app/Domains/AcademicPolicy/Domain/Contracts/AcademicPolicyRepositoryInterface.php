<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Domain\Contracts;

use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;

/**
 * Persistence contract of the Academic Policies engine (SPR-009.5).
 *
 * The repository owns the storage queries of the engine. Cross-domain readers
 * must use the {@see AcademicPolicyQueryContract} read-only gateway instead.
 */
interface AcademicPolicyRepositoryInterface
{
    public function find(string $schoolId, int|string $policyId): ?AcademicPolicy;

    public function findByKey(string $schoolId, string $key): ?AcademicPolicy;

    /**
     * Whether another active policy already occupies the key in the scope.
     */
    public function scopeTaken(
        string $schoolId,
        string $key,
        string $scope,
        ?int $academicYearId,
        ?int $subjectId,
        int|string|null $excludeId = null,
    ): bool;
}
