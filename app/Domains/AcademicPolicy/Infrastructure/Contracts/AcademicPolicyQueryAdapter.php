<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Infrastructure\Contracts;

use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyQueryContract;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyScope;
use App\Domains\AcademicPolicy\Domain\References\AcademicPolicyReference;
use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;

/**
 * Maps the AcademicPolicy model onto the read-only query contract (SPR-009.5).
 *
 * This adapter is the only place the engine's persistence is turned into the
 * consumer-facing reference shape. Every lookup is scoped to the requested
 * school, so a policy from one school can never leak to another.
 */
final class AcademicPolicyQueryAdapter implements AcademicPolicyQueryContract
{
    public function policies(string $schoolId, array $keys = []): array
    {
        return AcademicPolicy::query()
            ->forSchool($schoolId)
            ->where('is_active', true)
            ->when($keys !== [], fn ($query) => $query->whereIn('key', $keys))
            ->orderBy('key')
            ->get()
            ->map(fn (AcademicPolicy $policy): AcademicPolicyReference => $this->toReference($policy))
            ->all();
    }

    public function policy(string $schoolId, string $key): ?AcademicPolicyReference
    {
        $policy = AcademicPolicy::query()
            ->forSchool($schoolId)
            ->where('key', $key)
            ->where('scope', AcademicPolicyScope::School->value)
            ->where('is_active', true)
            ->first();

        return $policy !== null ? $this->toReference($policy) : null;
    }

    public function forPeriod(string $schoolId, int|string $yearId, int|string $termId): array
    {
        return AcademicPolicy::query()
            ->forSchool($schoolId)
            ->where('is_active', true)
            ->where(function ($query) use ($yearId): void {
                $query->where('scope', AcademicPolicyScope::School->value)
                    ->orWhere(fn ($sub) => $sub
                        ->where('scope', AcademicPolicyScope::AcademicYear->value)
                        ->where('academic_year_id', $yearId));
            })
            ->orderBy('key')
            ->get()
            ->map(fn (AcademicPolicy $policy): AcademicPolicyReference => $this->toReference($policy))
            ->all();
    }

    private function toReference(AcademicPolicy $policy): AcademicPolicyReference
    {
        return new AcademicPolicyReference(
            key: $policy->key,
            label: $policy->label,
            value: $policy->value,
            valueType: $policy->value_type,
            scope: $policy->scope,
            description: $policy->description,
            academicYearId: $policy->academic_year_id !== null ? (int) $policy->academic_year_id : null,
            subjectId: $policy->academic_subject_id !== null ? (int) $policy->academic_subject_id : null,
        );
    }
}
