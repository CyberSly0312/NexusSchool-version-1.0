<?php

declare(strict_types=1);

use App\Domains\AcademicPolicy\Application\DTOs\AcademicPolicyData;
use App\Domains\AcademicPolicy\Application\Services\AcademicPolicyService;
use App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyQueryContract;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyScope;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;
use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;

beforeEach(function (): void {
    $this->service = app(AcademicPolicyService::class);
});

function makePolicy(string $schoolId, string $key, float $value = 20.0): App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy
{
    return app(AcademicPolicyService::class)->create(AcademicPolicyData::fromArray([
        'school_id' => $schoolId,
        'key' => $key,
        'label' => 'Politique '.$key,
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => $value,
        'scope' => AcademicPolicyScope::School->value,
    ]));
}

test('policies do not leak across tenants', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $policyA = makePolicy($schoolA, 'grading_scale');

    expect(AcademicPolicy::query()->forSchool($schoolA)->count())->toBe(1)
        ->and(AcademicPolicy::query()->forSchool($schoolB)->count())->toBe(0)
        ->and(AcademicPolicy::query()->count())->toBe(1)
        ->and($policyA->school_id)->toBe($schoolA);
});

test('the query contract only returns the policies of the requested school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();
    $query = app(AcademicPolicyQueryContract::class);

    makePolicy($schoolA, 'grading_scale');
    makePolicy($schoolA, 'honor_threshold');
    makePolicy($schoolB, 'grading_scale');

    $policiesA = $query->policies($schoolA);
    $policiesB = $query->policies($schoolB);

    expect(array_map(static fn ($p): string => $p->key, $policiesA))->toBe(['grading_scale', 'honor_threshold'])
        ->and(array_map(static fn ($p): string => $p->key, $policiesB))->toBe(['grading_scale'])
        ->and($query->policy($schoolA, 'grading_scale'))->not->toBeNull()
        ->and($query->policy($schoolB, 'grading_scale'))->not->toBeNull();
});

test('an inactive policy is invisible to the query contract', function (): void {
    $schoolA = makeTenant();
    $query = app(AcademicPolicyQueryContract::class);

    $policy = makePolicy($schoolA, 'grading_scale');
    $policy->update(['is_active' => false]);

    expect($query->policy($schoolA, 'grading_scale'))->toBeNull()
        ->and($query->policies($schoolA))->toBe([]);
});

test('the duplicate key check is scoped to the school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    makePolicy($schoolA, 'grading_scale');

    expect(fn () => makePolicy($schoolA, 'grading_scale'))->toThrow(\App\Domains\AcademicPolicy\Domain\Exceptions\DuplicatePolicyKeyException::class)
        ->and(makePolicy($schoolB, 'grading_scale'))->toBeInstanceOf(AcademicPolicy::class);
});
