<?php

declare(strict_types=1);

use App\Domains\ClassCouncil\Application\Services\AutomaticDecisionService;
use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType;
use App\Domains\ClassCouncil\Domain\References\GradebookStandingReference;

function standing(float $percentage, int $failedSubjects = 0): GradebookStandingReference
{
    return new GradebookStandingReference(
        overallPercentage: $percentage,
        failedSubjects: $failedSubjects,
    );
}

test('no standing means no automatic decision', function (): void {
    $service = new AutomaticDecisionService();

    expect($service->propose(null))->toBeNull();
});

test('a standing without a percentage yields no automatic decision', function (): void {
    $service = new AutomaticDecisionService();

    expect($service->propose(new GradebookStandingReference()))->toBeNull();
});

test('a student with a failing subject is automatically failed', function (): void {
    $service = new AutomaticDecisionService();

    $proposal = $service->propose(standing(68.0, failedSubjects: 1));

    expect($proposal)->not->toBeNull()
        ->and($proposal->type)->toBe(CouncilDecisionType::Failed)
        ->and($proposal->label())->toBe('Redoublement');
});

test('a student reaching the passing threshold with no failures is automatically passed', function (): void {
    config(['nexus-class-council.promotion.passing_percentage' => 50.0]);

    $service = new AutomaticDecisionService();

    $proposal = $service->propose(standing(75.5));

    expect($proposal)->not->toBeNull()
        ->and($proposal->type)->toBe(CouncilDecisionType::Passed)
        ->and($proposal->label())->toBe('Admis');
});

test('a student below the passing threshold is automatically failed', function (): void {
    config(['nexus-class-council.promotion.passing_percentage' => 50.0]);

    $service = new AutomaticDecisionService();

    expect($service->propose(standing(40.0))?->type)->toBe(CouncilDecisionType::Failed);
});
