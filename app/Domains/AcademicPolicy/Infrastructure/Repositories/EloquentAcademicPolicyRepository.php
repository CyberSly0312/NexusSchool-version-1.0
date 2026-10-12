<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Infrastructure\Repositories;

use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyRepositoryInterface;
use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;

final class EloquentAcademicPolicyRepository implements AcademicPolicyRepositoryInterface
{
    public function find(string $schoolId, int|string $policyId): ?AcademicPolicy
    {
        return AcademicPolicy::query()
            ->forSchool($schoolId)
            ->find($policyId);
    }

    public function findByKey(string $schoolId, string $key): ?AcademicPolicy
    {
        return AcademicPolicy::query()
            ->forSchool($schoolId)
            ->where('key', $key)
            ->first();
    }

    public function scopeTaken(
        string $schoolId,
        string $key,
        string $scope,
        ?int $academicYearId,
        ?int $subjectId,
        int|string|null $excludeId = null,
    ): bool {
        return AcademicPolicy::query()
            ->forSchool($schoolId)
            ->where('key', $key)
            ->where('scope', $scope)
            ->where('academic_year_id', $academicYearId)
            ->where('academic_subject_id', $subjectId)
            ->when($excludeId !== null, fn ($query) => $query->whereKeyNot($excludeId))
            ->exists();
    }
}
