<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Application\Actions;

use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyRepositoryInterface;
use App\Domains\AcademicPolicy\Domain\Events\AcademicPolicyArchived;
use App\Domains\AcademicPolicy\Domain\Exceptions\PolicyNotFoundException;
use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;
use App\Models\User;

/**
 * Archives (soft deletes) an academic policy of a school (SPR-009.5).
 *
 * Archiving deactivates the policy for every consumer: queries only return
 * active policies. Historical audit records are preserved.
 */
final class ArchiveAcademicPolicyAction
{
    public function __construct(
        private readonly AcademicPolicyRepositoryInterface $repository,
    ) {
    }

    public function execute(string $schoolId, int|string $policyId, ?User $actor = null): AcademicPolicy
    {
        $policy = $this->repository->find($schoolId, $policyId);

        if ($policy === null) {
            throw PolicyNotFoundException::forId($schoolId, (string) $policyId);
        }

        $policy->update(['is_active' => false]);
        $policy->delete();

        AcademicPolicyArchived::dispatch($policy, $actor);

        return $policy;
    }
}
