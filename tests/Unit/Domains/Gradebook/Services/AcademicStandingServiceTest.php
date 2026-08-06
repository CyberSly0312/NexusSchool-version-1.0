<?php

declare(strict_types=1);

use App\Domains\Gradebook\Application\Services\AcademicStandingService;
use App\Domains\Gradebook\Application\Services\GradeCalculationService;
use App\Domains\Gradebook\Domain\Enums\AcademicStanding;
use App\Domains\Gradebook\Domain\References\StandingContributionReference;

beforeEach(function (): void {
    $this->standing = new AcademicStandingService(new GradeCalculationService());
});

function standingContribution(float $percentage, float $gradePoint, float $coefficient = 1.0): StandingContributionReference
{
    return new StandingContributionReference(
        percentage: $percentage,
        gradePoint: $gradePoint,
        coefficient: $coefficient,
    );
}

test('the GPA is the coefficient-weighted mean of the grade points', function (): void {
    $gpa = $this->standing->gpa([
        standingContribution(80, 3.7, 2.0),
        standingContribution(60, 3.0, 1.0),
    ]);

    expect($gpa)->not->toBeNull()
        ->and($gpa->gpa)->toBe(round((3.7 * 2 + 3.0 * 1) / 3, 2))
        ->and($gpa->gradeCount)->toBe(2)
        ->and($gpa->totalCoefficient)->toBe(3.0)
        ->and($gpa->scale)->toBe(4.0);
});

test('the GPA falls back to a plain mean when every coefficient is zero', function (): void {
    $gpa = $this->standing->gpa([
        standingContribution(80, 4.0, 0.0),
        standingContribution(60, 2.0, 0.0),
    ]);

    expect($gpa->gpa)->toBe(3.0);
});

test('the GPA is null when there is no contribution', function (): void {
    expect($this->standing->gpa([]))->toBeNull();
});

test('the CGPA is the GPA over every contribution with no period filter', function (): void {
    $year = [
        standingContribution(90, 4.0, 2.0),
        standingContribution(70, 3.3, 1.0),
    ];

    $termOne = [$year[0]];
    $termTwo = [$year[1]];

    $expectedYear = round((4.0 * 2 + 3.3 * 1) / 3, 2);

    expect($this->standing->cgpa($termOne)->gpa)->toBe(4.0)
        ->and($this->standing->cgpa($termTwo)->gpa)->toBe(3.3)
        ->and($this->standing->cgpa($year)->gpa)->toBe($expectedYear);
});

test('the overall percentage is the coefficient-weighted mean of the percentages', function (): void {
    $percentage = $this->standing->overallPercentage([
        standingContribution(80, 3.7, 2.0),
        standingContribution(60, 3.0, 1.0),
    ]);

    expect($percentage)->toBe(round((80 * 2 + 60 * 1) / 3, 2));
});

test('the overall percentage is null when there is no contribution', function (): void {
    expect($this->standing->overallPercentage([]))->toBeNull();
});

test('a high average with no failures is a distinction', function (): void {
    expect($this->standing->standing([
        standingContribution(85, 3.7, 2.0),
        standingContribution(82, 3.7, 1.0),
    ]))->toBe(AcademicStanding::Distinction);
});

test('an average above the passing threshold is a pass', function (): void {
    expect($this->standing->standing([
        standingContribution(60, 3.0),
        standingContribution(55, 2.5),
    ]))->toBe(AcademicStanding::Pass);
});

test('one failing subject becomes conditional while the average passes', function (): void {
    expect($this->standing->standing([
        standingContribution(65, 3.3),
        standingContribution(40, 1.5),
    ]))->toBe(AcademicStanding::Conditional);
});

test('an average below the passing threshold is a fail', function (): void {
    expect($this->standing->standing([
        standingContribution(45, 2.0),
        standingContribution(30, 1.0),
    ]))->toBe(AcademicStanding::Fail);
});

test('no contribution yields no standing', function (): void {
    expect($this->standing->standing([]))->toBeNull();
});
