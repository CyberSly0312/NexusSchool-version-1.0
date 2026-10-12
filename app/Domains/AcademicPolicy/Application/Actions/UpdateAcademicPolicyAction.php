<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Application\Actions;

use App\Domains\AcademicPolicy\Application\DTOs\AcademicPolicyData;
use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyRepositoryInterface;
use App\Domains\AcademicPolicy\Domain\Events\AcademicPolicyUpdated;
use App\Domains\AcademicPolicy\Domain\Exceptions\DuplicatePolicyKeyException;
use App\Domains\AcademicPolicy\Domain\Exceptions\PolicyNotFoundException;
use App\Domains\AcademicPolicy\Domain\ValueObjects\PolicyValue;
use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Updates an academic policy of a school (SPR-009.5).
 *
 * The policy is looked up inside the school before any change; the updated
 * value is coerced onto its declared type and the key must stay free within
 * the scope (excluding the policy itself).
 */
final class UpdateAcademicPolicyAction
{
    public function __construct(
        private readonly AcademicPolicyRepositoryInterface $repository,
    ) {
    }

    public function execute(string $schoolId, int|string $policyId, AcademicPolicyData $data, ?User $actor = null): AcademicPolicy
    {
        $policy = $this->repository->find($schoolId, $policyId);

        if ($policy === null) {
            throw PolicyNotFoundException::forId($schoolId, (string) $policyId);
        }

        if ($this->repository->scopeTaken(
            $data->schoolId,
            $data->key,
            $data->scope->value,
            $data->academicYearId,
            $data->subjectId,
            excludeId: $policy->getKey(),
        )) {
            throw DuplicatePolicyKeyException::forSchool($data->schoolId, $data->key);
        }

        $value = PolicyValue::coerce($data->valueType, $data->value);

        return DB::transaction(function () use ($policy, $data, $value, $actor): AcademicPolicy {
            $policy->update([
                'key' => $data->key,
                'label' => $data->label,
                'description' => $data->description,
                'value_type' => $data->valueType->value,
                'value' => $value->asRaw(),
                'scope' => $data->scope->value,
                'academic_year_id' => $data->academicYearId,
                'academic_subject_id' => $data->subjectId,
                'is_active' => $data->isActive,
                'updated_by' => $actor?->getKey(),
            ]);

            AcademicPolicyUpdated::dispatch($policy, $actor);

            return $policy;
        });
    }
}
