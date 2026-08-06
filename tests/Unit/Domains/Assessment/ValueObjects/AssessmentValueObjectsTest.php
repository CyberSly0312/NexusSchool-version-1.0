<?php

declare(strict_types=1);

use App\Domains\Assessment\Domain\ValueObjects\AssessmentPeriod;
use App\Domains\Assessment\Domain\ValueObjects\MaximumScore;
use App\Domains\Assessment\Domain\ValueObjects\PassingScore;
use App\Domains\Assessment\Domain\ValueObjects\ScorePercentage;
use App\Domains\Assessment\Domain\ValueObjects\Weight;
use Carbon\CarbonImmutable;

test('maximum score accepts a finite strictly positive value and rounds it', function (): void {
    expect(MaximumScore::fromFloat(20.0)->value)->toBe(20.0)
        ->and(MaximumScore::fromFloat(19.999)->value)->toBe(20.0)
        ->and((string) MaximumScore::fromFloat(10.0))->toBe('10');
});

test('maximum score rejects zero, negative and non-finite values', function (): void {
    MaximumScore::fromFloat(0.0);
})->throws(InvalidArgumentException::class);

test('maximum score rejects a negative value', function (): void {
    MaximumScore::fromFloat(-5.0);
})->throws(InvalidArgumentException::class);

test('passing score must be non-negative and never exceed the maximum', function (): void {
    expect(PassingScore::fromFloat(10.0, 20.0)->value)->toBe(10.0)
        ->and(PassingScore::fromFloat(0.0, 20.0)->value)->toBe(0.0);
});

test('passing score rejects a value above the maximum', function (): void {
    PassingScore::fromFloat(21.0, 20.0);
})->throws(InvalidArgumentException::class);

test('passing score rejects a negative value', function (): void {
    PassingScore::fromFloat(-1.0, 20.0);
})->throws(InvalidArgumentException::class);

test('weight stays within the configured ceiling and exposes its fraction', function (): void {
    $weight = Weight::fromPercent(30.0);

    expect($weight->value)->toBe(30.0)
        ->and($weight->fraction())->toBe(0.3)
        ->and((string) $weight)->toBe('30');
});

test('weight rejects values outside the configured ceiling', function (): void {
    Weight::fromPercent(101.0);
})->throws(InvalidArgumentException::class);

test('weight rejects a negative value', function (): void {
    Weight::fromPercent(-1.0);
})->throws(InvalidArgumentException::class);

test('score percentage converts a raw score into a ratio clamped in [0, 100]', function (): void {
    expect(ScorePercentage::fromScore(10.0, 20.0)->value)->toBe(50.0)
        ->and(ScorePercentage::fromScore(0.0, 20.0)->value)->toBe(0.0)
        ->and(ScorePercentage::fromScore(20.0, 20.0)->value)->toBe(100.0)
        ->and(ScorePercentage::fromScore(25.0, 20.0)->value)->toBe(100.0)
        ->and(ScorePercentage::fromScore(-5.0, 20.0)->value)->toBe(0.0)
        ->and(ScorePercentage::fromScore(10.0, 20.0)->ratio())->toBe(0.5)
        ->and(ScorePercentage::fromScore(10.0, 20.0)->isBelow(60.0))->toBeTrue();
});

test('score percentage requires a strictly positive maximum', function (): void {
    ScorePercentage::fromScore(10.0, 0.0);
})->throws(InvalidArgumentException::class);

test('assessment period validates its window and scopes dates', function (): void {
    $period = new AssessmentPeriod(
        CarbonImmutable::parse('2026-09-01'),
        CarbonImmutable::parse('2026-12-20'),
        AssessmentPeriod::TYPE_TERM,
    );

    expect($period->type)->toBe(AssessmentPeriod::TYPE_TERM)
        ->and($period->days())->toBe(111)
        ->and($period->contains(CarbonImmutable::parse('2026-09-10')))->toBeTrue()
        ->and($period->contains(CarbonImmutable::parse('2027-01-01')))->toBeFalse()
        ->and($period->contains(CarbonImmutable::parse('2026-08-31')))->toBeFalse();
});

test('assessment period forTerm marks the term type', function (): void {
    $period = AssessmentPeriod::forTerm(
        CarbonImmutable::parse('2026-09-01'),
        CarbonImmutable::parse('2026-12-20'),
    );

    expect($period->type)->toBe(AssessmentPeriod::TYPE_TERM);
});

test('assessment period rejects a start after its end', function (): void {
    new AssessmentPeriod(
        CarbonImmutable::parse('2026-10-01'),
        CarbonImmutable::parse('2026-09-01'),
    );
})->throws(InvalidArgumentException::class);
