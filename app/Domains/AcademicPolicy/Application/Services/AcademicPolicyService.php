<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Application\Services;

use App\Domains\AcademicPolicy\Application\Actions\ArchiveAcademicPolicyAction;
use App\Domains\AcademicPolicy\Application\Actions\CreateAcademicPolicyAction;
use App\Domains\AcademicPolicy\Application\Actions\UpdateAcademicPolicyAction;
use App\Domains\AcademicPolicy\Application\DTOs\AcademicPolicyData;
use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;
use App\Models\User;

/**
 * Application facade of the Academic Policies engine (SPR-009.5).
 *
 * A thin facade over the single-use actions. Read access from other domains
 * happens through the {@see AcademicPolicyQueryContract}; read access inside
 * the domain is handled by the repository.
 */
final class AcademicPolicyService
{
    public function __construct(
        private readonly CreateAcademicPolicyAction $create,
        private readonly UpdateAcademicPolicyAction $update,
        private readonly ArchiveAcademicPolicyAction $archive,
    ) {
    }

    public function create(AcademicPolicyData $data, ?User $actor = null): AcademicPolicy
    {
        return $this->create->execute($data, $actor);
    }

    public function update(string $schoolId, int|string $policyId, AcademicPolicyData $data, ?User $actor = null): AcademicPolicy
    {
        return $this->update->execute($schoolId, $policyId, $data, $actor);
    }

    public function archive(string $schoolId, int|string $policyId, ?User $actor = null): AcademicPolicy
    {
        return $this->archive->execute($schoolId, $policyId, $actor);
    }
}
