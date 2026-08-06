<?php

declare(strict_types=1);

use App\Domains\Assessment\Application\Services\AssessmentStatisticsCalculator;
use App\Domains\Assessment\Application\Services\ScoreValidationService;
use App\Domains\Assessment\Domain\Enums\ScoreValidationStatus;

test('score validation flags non-finite and negative scores as below zero', function (): void {
    $validation = new ScoreValidationService();

    expect($validation->validate(12.0, 20.0))->toBe(ScoreValidationStatus::Valid)
        ->and($validation->validate(0.0, 20.0))->toBe(ScoreValidationStatus::Valid)
        ->and($validation->validate(20.0, 20.0))->toBe(ScoreValidationStatus::Valid)
        ->and($validation->validate(-0.1, 20.0))->toBe(ScoreValidationStatus::BelowZero)
        ->and($validation->validate(NAN, 20.0))->toBe(ScoreValidationStatus::BelowZero)
        ->and($validation->validate(INF, 20.0))->toBe(ScoreValidationStatus::BelowZero);
});

test('score validation flags scores above the maximum as invalid', function (): void {
    $validation = new ScoreValidationService();

    expect($validation->validate(20.1, 20.0))->toBe(ScoreValidationStatus::AboveMaximum);
});

test('score validation flags scores below the passing threshold as below passing', function (): void {
    $validation = new ScoreValidationService();

    expect($validation->validate(9.5, 20.0, 10.0))->toBe(ScoreValidationStatus::BelowPassing)
        ->and($validation->validate(10.0, 20.0, 10.0))->toBe(ScoreValidationStatus::Valid);
});

test('score validation without a passing threshold never flags below passing', function (): void {
    $validation = new ScoreValidationService();

    expect($validation->validate(2.0, 20.0))->toBe(ScoreValidationStatus::Valid);
});

test('statistics calculator computes the average, min, max and median', function (): void {
    $calculator = new AssessmentStatisticsCalculator();

    $rows = [
        ['score' => 10.0, 'maximum' => 20.0],
        ['score' => 14.0, 'maximum' => 20.0],
        ['score' => 18.0, 'maximum' => 20.0],
    ];

    expect($calculator->average($rows))->toBe(14.0)
        ->and($calculator->minimum($rows))->toBe(10.0)
        ->and($calculator->maximum($rows))->toBe(18.0)
        ->and($calculator->median($rows))->toBe(14.0);
});

test('statistics calculator returns zero aggregates for empty input', function (): void {
    $calculator = new AssessmentStatisticsCalculator();

    expect($calculator->average([]))->toBe(0.0)
        ->and($calculator->minimum([]))->toBe(0.0)
        ->and($calculator->maximum([]))->toBe(0.0)
        ->and($calculator->median([]))->toBe(0.0)
        ->and($calculator->passRate([]))->toBe(0.0);
});

test('statistics calculator averages an even row set', function (): void {
    $calculator = new AssessmentStatisticsCalculator();

    $rows = [
        ['score' => 10.0, 'maximum' => 20.0],
        ['score' => 16.0, 'maximum' => 20.0],
    ];

    expect($calculator->median($rows))->toBe(13.0);
});

test('pass rate uses each row passing score when provided', function (): void {
    $calculator = new AssessmentStatisticsCalculator();

    $rows = [
        ['score' => 12.0, 'maximum' => 20.0, 'passing' => 10.0],
        ['score' => 9.0, 'maximum' => 20.0, 'passing' => 10.0],
        ['score' => 10.0, 'maximum' => 20.0, 'passing' => 10.0],
    ];

    expect($calculator->passRate($rows))->toBe(66.67)
        ->and($calculator->passes($rows[0]))->toBeTrue()
        ->and($calculator->passes($rows[1]))->toBeFalse();
});

test('pass rate falls back to the default passing fraction of the maximum', function (): void {
    $calculator = new AssessmentStatisticsCalculator();

    $rows = [
        ['score' => 11.0, 'maximum' => 20.0],
        ['score' => 9.0, 'maximum' => 20.0],
    ];

    expect($calculator->passRate($rows))->toBe(50.0);
});

test('passes is false for a non-positive maximum', function (): void {
    $calculator = new AssessmentStatisticsCalculator();

    expect($calculator->passes(['score' => 0.0, 'maximum' => 0.0]))->toBeFalse();
});
