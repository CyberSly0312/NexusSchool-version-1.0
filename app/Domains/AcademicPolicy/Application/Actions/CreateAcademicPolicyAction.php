<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Application\Actions;

use App\Domains\AcademicPolicy\Application\DTOs\AcademicPolicyData;
use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyRepositoryInterface;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyScope;
use App\Domains\AcademicPolicy\Domain\Events\AcademicPolicyCreated;
use App\Domains\AcademicPolicy\Domain\Exceptions\DuplicatePolicyKeyException;
use App\Domains\AcademicPolicy\Domain\Exceptions\InvalidPolicyScopeException;
use App\Domains\AcademicPolicy\Domain\ValueObjects\PolicyValue;
use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Creates an academic policy for a school (SPR-009.5).
 *
 * The value is coerced onto the declared type, the scope coherence is enforced
 * and the key must be free within the scope. The policy is always bound to the
 * school passed in the data.
 */
final class CreateAcademicPolicyAction
{
    public function __construct(
        private readonly AcademicPolicyRepositoryInterface $repository,
    ) {
    }

    public function execute(AcademicPolicyData $data, ?User $actor = null): AcademicPolicy
    {
        $this->assertKey($data->key);
        $this->assertLabel($data->label);
        $this->assertScopeCoherence($data);

        $value = PolicyValue::coerce($data->valueType, $data->value);

        if ($this->repository->scopeTaken(
            $data->schoolId,
            $data->key,
            $data->scope->value,
            $data->academicYearId,
            $data->subjectId,
        )) {
            throw DuplicatePolicyKeyException::forSchool($data->schoolId, $data->key);
        }

        return DB::transaction(function () use ($data, $value, $actor): AcademicPolicy {
            $policy = AcademicPolicy::create([
                'school_id' => $data->schoolId,
                'key' => $data->key,
                'label' => $data->label,
                'description' => $data->description,
                'value_type' => $data->valueType->value,
                'value' => $value->asRaw(),
                'scope' => $data->scope->value,
                'academic_year_id' => $data->academicYearId,
                'academic_subject_id' => $data->subjectId,
                'is_active' => $data->isActive,
                'created_by' => $actor?->getKey(),
            ]);

            AcademicPolicyCreated::dispatch($policy, $actor);

            return $policy;
        });
    }

    private function assertScopeCoherence(AcademicPolicyData $data): void
    {
        match ($data->scope) {
            AcademicPolicyScope::School => $data->academicYearId === null && $data->subjectId === null
                ? null
                : throw InvalidPolicyScopeException::forScope(AcademicPolicyScope::School->value),

            AcademicPolicyScope::AcademicYear => $data->academicYearId !== null
                ? null
                : throw InvalidPolicyScopeException::forScope(AcademicPolicyScope::AcademicYear->value),

            AcademicPolicyScope::Subject => $data->subjectId !== null
                ? null
                : throw InvalidPolicyScopeException::forScope(AcademicPolicyScope::Subject->value),
        };
    }

    private function assertKey(string $key): void
    {
        $max = (int) config('nexus-academic-policy.policy.key_max_length', 100);

        if ($key === '' || mb_strlen($key) > $max) {
            throw new \InvalidArgumentException(
                sprintf('The policy key must be between 1 and %d characters.', $max),
            );
        }
    }

    private function assertLabel(string $label): void
    {
        $max = (int) config('nexus-academic-policy.policy.label_max_length', 255);

        if ($label === '' || mb_strlen($label) > $max) {
            throw new \InvalidArgumentException(
                sprintf('The policy label must be between 1 and %d characters.', $max),
            );
        }
    }
}
