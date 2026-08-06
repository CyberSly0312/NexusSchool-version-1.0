<?php

declare(strict_types=1);

use App\Domains\Gradebook\Application\Services\GradeCalculationService;
use App\Domains\Gradebook\Application\Services\GradeRankingService;
use App\Domains\Gradebook\Domain\Exceptions\InvalidPercentageException;
use App\Domains\Gradebook\Domain\References\AssessmentReference;
use App\Domains\Gradebook\Domain\ValueObjects\Average;
use App\Domains\Gradebook\Domain\ValueObjects\GradePercentage;

beforeEach(function (): void {
    $this->calculator = new GradeCalculationService();
});

function contribution(float $percentage, float $weight = 1.0): AssessmentReference
{
    return new AssessmentReference(
        id: fake()->unique()->numberBetween(1, 100000),
        title: 'Assessment',
        percentage: $percentage,
        weight: $weight,
        maximumScore: 20.0,
    );
}

test('empty contributions yield no percentage', function (): void {
    expect($this->calculator->calculateTermPercentage([]))->toBeNull();
});

test('the term percentage is the weighted mean of the contributions', function (): void {
    $percentage = $this->calculator->calculateTermPercentage([
        contribution(80, 1.0),
        contribution(60, 3.0),
    ]);

    expect($percentage)->toBe(65.0);
});

test('unweighted contributions fall back to the plain mean', function (): void {
    $percentage = $this->calculator->calculateTermPercentage([
        contribution(90, 0.0),
        contribution(50, 0.0),
    ]);

    expect($percentage)->toBe(70.0);
});

test('a single contribution is its own percentage', function (): void {
    expect($this->calculator->calculateTermPercentage([contribution(42.5)]))->toBe(42.5);
});

test('the weighted mean of term averages uses the coefficients', function (): void {
    $mean = $this->calculator->calculateWeightedMean(
        [60.0, 80.0],
        [2.0, 1.0],
    );

    expect($mean)->toBe(round((60 * 2 + 80 * 1) / 3, 2));
});

test('the weighted mean returns null for empty inputs', function (): void {
    expect($this->calculator->calculateWeightedMean([], []))->toBeNull();
});

test('a percentage is clamped into [0, 100]', function (): void {
    expect((new GradeCalculationService())->calculateTermPercentage([contribution(120)]))->toBe(100.0);
});

test('grade percentage rejects out of range values', function (): void {
    expect(fn () => GradePercentage::fromValue(101))->toThrow(InvalidPercentageException::class)
        ->and(fn () => GradePercentage::fromValue(-1))->toThrow(InvalidPercentageException::class);
});

test('average projects a percentage onto the school scale', function (): void {
    expect(Average::fromPercentage(80, 20)->value)->toBe(16.0)
        ->and(Average::fromPercentage(80, 100)->value)->toBe(80.0)
        ->and(Average::fromPercentage(60, 20)->value)->toBe(12.0);
});

test('ranks follow standard competition ranking', function (): void {
    $ranking = new GradeRankingService();

    $ranks = $ranking->rank([
        's1' => 85.0,
        's2' => 85.0,
        's3' => 70.0,
        's4' => 60.0,
    ]);

    expect($ranks['s1']->rank)->toBe(1)
        ->and($ranks['s2']->rank)->toBe(1)
        ->and($ranks['s3']->rank)->toBe(3)
        ->and($ranks['s4']->rank)->toBe(4)
        ->and($ranks['s4']->denominator)->toBe(4);
});

test('ranking an empty set yields an empty set', function (): void {
    expect((new GradeRankingService())->rank([]))->toBe([]);
});
