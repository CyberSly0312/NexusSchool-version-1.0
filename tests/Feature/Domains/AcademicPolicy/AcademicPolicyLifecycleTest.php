<?php

declare(strict_types=1);

use App\Domains\AcademicPolicy\Application\DTOs\AcademicPolicyData;
use App\Domains\AcademicPolicy\Application\Services\AcademicPolicyService;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyScope;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;
use App\Domains\AcademicPolicy\Domain\Exceptions\DuplicatePolicyKeyException;
use App\Domains\AcademicPolicy\Domain\Exceptions\InvalidPolicyScopeException;
use App\Domains\AcademicPolicy\Domain\Exceptions\InvalidPolicyValueException;
use App\Domains\AcademicPolicy\Domain\Exceptions\PolicyNotFoundException;
use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;

beforeEach(function (): void {
    $this->service = app(AcademicPolicyService::class);
});

test('a policy is created with its typed value for a school', function (): void {
    $schoolId = makeTenant();

    $policy = $this->service->create(AcademicPolicyData::fromArray([
        'school_id' => $schoolId,
        'key' => 'grading_scale',
        'label' => 'Échelle de notation',
        'description' => 'Échelle sur 20 points.',
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => '20.0',
        'scope' => AcademicPolicyScope::School->value,
    ]));

    expect($policy->school_id)->toBe($schoolId)
        ->and($policy->key)->toBe('grading_scale')
        ->and($policy->value_type)->toBe(AcademicPolicyValueType::Float)
        ->and($policy->value)->toBe(20.0)
        ->and($policy->scope)->toBe(AcademicPolicyScope::School)
        ->and($policy->is_active)->toBeTrue()
        ->and($policy->uuid)->not->toBeNull();
});

test('a policy rejects a duplicate key inside the same school and scope', function (): void {
    $schoolId = makeTenant();
    $data = [
        'school_id' => $schoolId,
        'key' => 'honor_threshold',
        'label' => 'Seuil de mention',
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => 80.0,
        'scope' => AcademicPolicyScope::School->value,
    ];

    $this->service->create(AcademicPolicyData::fromArray($data));

    expect(fn () => $this->service->create(AcademicPolicyData::fromArray($data)))
        ->toThrow(DuplicatePolicyKeyException::class);
});

test('a policy allows the same key in distinct scopes', function (): void {
    $schoolId = makeTenant();
    $year = app(\App\Domains\Academic\Application\Services\CalendarService::class)->createYear(\App\Domains\Academic\Application\DTOs\CalendarYearData::fromArray([
        'school_id' => $schoolId,
        'name' => '2027-2028',
        'start_date' => '2027-09-01',
        'end_date' => '2028-07-31',
    ]));

    $this->service->create(AcademicPolicyData::fromArray([
        'school_id' => $schoolId,
        'key' => 'grading_scale',
        'label' => 'Échelle école',
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => 20.0,
        'scope' => AcademicPolicyScope::School->value,
    ]));

    $yearScoped = $this->service->create(AcademicPolicyData::fromArray([
        'school_id' => $schoolId,
        'key' => 'grading_scale',
        'label' => 'Échelle 2027-2028',
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => 100.0,
        'scope' => AcademicPolicyScope::AcademicYear->value,
        'academic_year_id' => $year->id,
    ]));

    expect($yearScoped->academic_year_id)->toBe((int) $year->id)
        ->and(AcademicPolicy::query()->forSchool($schoolId)->count())->toBe(2);
});

test('a policy rejects a value that does not match the declared type', function (): void {
    $schoolId = makeTenant();

    expect(fn () => $this->service->create(AcademicPolicyData::fromArray([
        'school_id' => $schoolId,
        'key' => 'assessment_rules',
        'label' => 'Règles d’évaluation',
        'value_type' => AcademicPolicyValueType::Boolean->value,
        'value' => 'not-a-boolean',
        'scope' => AcademicPolicyScope::School->value,
    ])))->toThrow(InvalidPolicyValueException::class);
});

test('a school-scoped policy rejects a scope reference and vice versa', function (): void {
    $schoolId = makeTenant();

    expect(fn () => $this->service->create(AcademicPolicyData::fromArray([
        'school_id' => $schoolId,
        'key' => 'grading_scale',
        'label' => 'Échelle',
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => 20.0,
        'scope' => AcademicPolicyScope::School->value,
        'academic_year_id' => 1,
    ])))->toThrow(InvalidPolicyScopeException::class);

    expect(fn () => $this->service->create(AcademicPolicyData::fromArray([
        'school_id' => $schoolId,
        'key' => 'grading_scale',
        'label' => 'Échelle',
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => 20.0,
        'scope' => AcademicPolicyScope::AcademicYear->value,
    ])))->toThrow(InvalidPolicyScopeException::class);
});

test('a policy can be updated with a new typed value', function (): void {
    $schoolId = makeTenant();
    $policy = $this->service->create(AcademicPolicyData::fromArray([
        'school_id' => $schoolId,
        'key' => 'grading_scale',
        'label' => 'Échelle',
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => 20.0,
        'scope' => AcademicPolicyScope::School->value,
    ]));

    $updated = $this->service->update($schoolId, $policy->id, AcademicPolicyData::fromArray([
        'school_id' => $schoolId,
        'key' => 'grading_scale',
        'label' => 'Échelle révisée',
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => '20',
        'scope' => AcademicPolicyScope::School->value,
        'is_active' => false,
    ]));

    expect($updated->label)->toBe('Échelle révisée')
        ->and($updated->value)->toBe(20.0)
        ->and($updated->is_active)->toBeFalse();
});

test('a policy cannot be updated across schools', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();
    $policy = $this->service->create(AcademicPolicyData::fromArray([
        'school_id' => $schoolA,
        'key' => 'grading_scale',
        'label' => 'Échelle',
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => 20.0,
        'scope' => AcademicPolicyScope::School->value,
    ]));

    expect(fn () => $this->service->update($schoolB, $policy->id, AcademicPolicyData::fromArray([
        'school_id' => $schoolB,
        'key' => 'grading_scale',
        'label' => 'Échelle B',
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => 20.0,
        'scope' => AcademicPolicyScope::School->value,
    ])))->toThrow(PolicyNotFoundException::class);
});

test('an archived policy is deactivated and soft deleted', function (): void {
    $schoolId = makeTenant();
    $policy = $this->service->create(AcademicPolicyData::fromArray([
        'school_id' => $schoolId,
        'key' => 'grading_scale',
        'label' => 'Échelle',
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => 20.0,
        'scope' => AcademicPolicyScope::School->value,
    ]));

    $this->service->archive($schoolId, $policy->id);

    expect($policy->refresh()->is_active)->toBeFalse()
        ->and($policy->refresh()->trashed())->toBeTrue();
});
