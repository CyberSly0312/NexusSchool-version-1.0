<?php

declare(strict_types=1);

use App\Domains\ClassCouncil\Application\Services\AutomaticDecisionService;
use App\Domains\ClassCouncil\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType;
use App\Domains\ClassCouncil\Domain\References\GradebookStandingReference;
use App\Domains\ClassCouncil\Domain\References\PolicyReference;

final class FakePromotionPoliciesContract implements AcademicPoliciesContract
{
    /**
     * @param  array<string, array<string, PolicyReference>>  $policies  Keyed by school then policy key.
     */
    public function __construct(private readonly array $policies = [])
    {
    }

    public function policy(string $schoolId, string $key): ?PolicyReference
    {
        return $this->policies[$schoolId][$key] ?? null;
    }
}

function promotionService(array $policies = []): AutomaticDecisionService
{
    return new AutomaticDecisionService(new FakePromotionPoliciesContract($policies));
}

function standing(float $percentage, int $failedSubjects = 0): GradebookStandingReference
{
    return new GradebookStandingReference(
        overallPercentage: $percentage,
        failedSubjects: $failedSubjects,
    );
}

function promotionPolicy(string $key, float $value): PolicyReference
{
    return new PolicyReference(key: $key, label: $key, value: $value, valueType: 'float');
}

beforeEach(function (): void {
    $this->schoolId = 'school-a';
});

test('no standing means no automatic decision', function (): void {
    $service = promotionService();

    expect($service->propose(null, $this->schoolId))->toBeNull();
});

test('a standing without a percentage yields no automatic decision', function (): void {
    $service = promotionService();

    expect($service->propose(new GradebookStandingReference(), $this->schoolId))->toBeNull();
});

test('a student with a failing subject is automatically failed', function (): void {
    $service = promotionService();

    $proposal = $service->propose(standing(68.0, failedSubjects: 1), $this->schoolId);

    expect($proposal)->not->toBeNull()
        ->and($proposal->type)->toBe(CouncilDecisionType::Failed)
        ->and($proposal->label())->toBe('Redoublement');
});

test('a student reaching the passing threshold with no failures is automatically passed', function (): void {
    config(['nexus-class-council.promotion.passing_percentage' => 50.0]);

    $service = promotionService();

    $proposal = $service->propose(standing(75.5), $this->schoolId);

    expect($proposal)->not->toBeNull()
        ->and($proposal->type)->toBe(CouncilDecisionType::Passed)
        ->and($proposal->label())->toBe('Admis');
});

test('a student below the passing threshold is automatically failed', function (): void {
    config(['nexus-class-council.promotion.passing_percentage' => 50.0]);

    $service = promotionService();

    expect($service->propose(standing(40.0), $this->schoolId)?->type)->toBe(CouncilDecisionType::Failed);
});

test('a school policy overrides the promotion passing threshold', function (): void {
    $service = promotionService([
        $this->schoolId => [
            'academic.class-council.passing_percentage' => promotionPolicy('academic.class-council.passing_percentage', 40.0),
        ],
    ]);

    expect($service->propose(standing(45.0), $this->schoolId)?->type)->toBe(CouncilDecisionType::Passed);
});

test('the promotion threshold is scoped: a policy of one school never leaks to another', function (): void {
    $service = promotionService([
        $this->schoolId => [
            'academic.class-council.passing_percentage' => promotionPolicy('academic.class-council.passing_percentage', 40.0),
        ],
    ]);

    expect($service->propose(standing(45.0), $this->schoolId)?->type)->toBe(CouncilDecisionType::Passed)
        ->and($service->propose(standing(45.0), 'other-school')?->type)->toBe(CouncilDecisionType::Failed);
});
