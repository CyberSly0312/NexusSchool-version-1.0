<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Domain\Contracts\AcademicPoliciesContract as AcademicCommunicationPoliciesContract;
use App\Domains\AcademicPolicy\Application\DTOs\AcademicPolicyData;
use App\Domains\AcademicPolicy\Application\Services\AcademicPolicyService;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;
use App\Domains\ReportCard\Domain\Contracts\AcademicPoliciesContract as ReportCardPoliciesContract;

beforeEach(function (): void {
    $this->reportCardPolicies = app(ReportCardPoliciesContract::class);
    $this->communicationPolicies = app(AcademicCommunicationPoliciesContract::class);
    $this->policyService = app(AcademicPolicyService::class);
});

function makeSchoolPolicy(string $schoolId, string $key, string $label, float $value): void
{
    app(AcademicPolicyService::class)->create(AcademicPolicyData::fromArray([
        'school_id' => $schoolId,
        'key' => $key,
        'label' => $label,
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => $value,
        'scope' => 'school',
    ]));
}

test('the report card contract returns the policies applicable to a period', function (): void {
    $schoolId = makeTenant();
    makeSchoolPolicy($schoolId, 'grading_scale', 'Échelle de notation', 20.0);

    $policies = $this->reportCardPolicies->forPeriod($schoolId, 1, 2);

    expect($policies)->toHaveCount(1)
        ->and($policies[0])->toBeInstanceOf(\App\Domains\ReportCard\Domain\References\PolicyReference::class)
        ->and($policies[0]->code)->toBe('grading_scale')
        ->and($policies[0]->label)->toBe('Échelle de notation')
        ->and($policies[0]->description)->toBeNull();
});

test('the report card contract returns an empty list when no policy is defined', function (): void {
    $schoolId = makeTenant();

    expect($this->reportCardPolicies->forPeriod($schoolId, 1, 2))->toBe([]);
});

test('the academic communication contract returns the policies by key', function (): void {
    $schoolId = makeTenant();
    makeSchoolPolicy($schoolId, 'honor_threshold', 'Seuil de mention', 80.0);

    $policies = $this->communicationPolicies->policies($schoolId, ['honor_threshold', 'assessment_rules']);

    expect($policies)->toHaveCount(1)
        ->and($policies[0])->toBeInstanceOf(\App\Domains\AcademicCommunication\Domain\References\AcademicPolicyReference::class)
        ->and($policies[0]->key)->toBe('honor_threshold')
        ->and($policies[0]->value)->toBe(80.0)
        ->and($policies[0]->scope)->toBe('school');

    $single = $this->communicationPolicies->policy($schoolId, 'honor_threshold');

    expect($single)->not->toBeNull()
        ->and($single->label)->toBe('Seuil de mention');
});

test('the academic communication contract returns an empty collection for unknown keys', function (): void {
    $schoolId = makeTenant();

    expect($this->communicationPolicies->policies($schoolId, ['grading_scale']))->toBe([])
        ->and($this->communicationPolicies->policy($schoolId, 'grading_scale'))->toBeNull();
});

test('the consumer adapters never leak a policy from another school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();
    makeSchoolPolicy($schoolA, 'grading_scale', 'Échelle A', 20.0);

    expect($this->reportCardPolicies->forPeriod($schoolB, 1, 2))->toBe([])
        ->and($this->communicationPolicies->policy($schoolB, 'grading_scale'))->toBeNull()
        ->and($this->reportCardPolicies->forPeriod($schoolA, 1, 2))->toHaveCount(1);
});
