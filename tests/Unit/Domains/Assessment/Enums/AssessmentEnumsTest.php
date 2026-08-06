<?php

declare(strict_types=1);

use App\Domains\Assessment\Domain\Enums\AssessmentSessionStatus;
use App\Domains\Assessment\Domain\Enums\AssessmentStatus;
use App\Domains\Assessment\Domain\Enums\AssessmentType;
use App\Domains\Assessment\Domain\Enums\ScoreStatus;
use App\Domains\Assessment\Domain\Enums\ScoreValidationStatus;

test('assessment status exposes a French label and a color for every case', function (): void {
    expect(AssessmentStatus::cases())->toHaveCount(5);

    foreach (AssessmentStatus::cases() as $status) {
        expect($status->label())->toBeString()->not->toBeEmpty()
            ->and($status->color())->toBeString()->not->toBeEmpty();
    }
});

test('assessment status options map every canonical status to its French label', function (): void {
    $options = AssessmentStatus::options();

    expect($options)->toBeArray()
        ->toHaveCount(5)
        ->and(array_values($options))->toContain('Brouillon', 'Publiée', 'Terminée', 'Archivée');
});

test('assessment lifecycle follows the canonical forward-only path', function (): void {
    $transitions = [
        [AssessmentStatus::Draft, AssessmentStatus::Scheduled, true],
        [AssessmentStatus::Draft, AssessmentStatus::Published, true],
        [AssessmentStatus::Scheduled, AssessmentStatus::Published, true],
        [AssessmentStatus::Scheduled, AssessmentStatus::Completed, true],
        [AssessmentStatus::Published, AssessmentStatus::Completed, true],
        [AssessmentStatus::Draft, AssessmentStatus::Completed, false],
        [AssessmentStatus::Completed, AssessmentStatus::Draft, false],
        [AssessmentStatus::Archived, AssessmentStatus::Published, false],
        [AssessmentStatus::Draft, AssessmentStatus::Draft, false],
    ];

    foreach ($transitions as [$from, $to, $expected]) {
        expect($to->allowsTransitionFrom($from))->toBe($expected);
    }
});

test('published, completed and archived assessments are definition-immutable', function (): void {
    expect(AssessmentStatus::Draft->isDefinitionImmutable())->toBeFalse()
        ->and(AssessmentStatus::Scheduled->isDefinitionImmutable())->toBeFalse()
        ->and(AssessmentStatus::Published->isDefinitionImmutable())->toBeTrue()
        ->and(AssessmentStatus::Completed->isDefinitionImmutable())->toBeTrue()
        ->and(AssessmentStatus::Archived->isDefinitionImmutable())->toBeTrue();
});

test('scores are recordable until the assessment is completed', function (): void {
    expect(AssessmentStatus::Draft->isScoreable())->toBeTrue()
        ->and(AssessmentStatus::Scheduled->isScoreable())->toBeTrue()
        ->and(AssessmentStatus::Published->isScoreable())->toBeTrue()
        ->and(AssessmentStatus::Completed->isScoreable())->toBeFalse()
        ->and(AssessmentStatus::Archived->isScoreable())->toBeFalse();
});

test('assessment session status exposes a French label and a color for every case', function (): void {
    expect(AssessmentSessionStatus::cases())->toHaveCount(4);

    foreach (AssessmentSessionStatus::cases() as $status) {
        expect($status->label())->toBeString()->not->toBeEmpty()
            ->and($status->color())->toBeString()->not->toBeEmpty();
    }
});

test('only scheduled and ongoing sessions are active', function (): void {
    expect(AssessmentSessionStatus::Scheduled->isActive())->toBeTrue()
        ->and(AssessmentSessionStatus::Ongoing->isActive())->toBeTrue()
        ->and(AssessmentSessionStatus::Completed->isActive())->toBeFalse()
        ->and(AssessmentSessionStatus::Cancelled->isActive())->toBeFalse();
});

test('session lifecycle allows only the canonical forward transitions', function (): void {
    $transitions = [
        [AssessmentSessionStatus::Scheduled, AssessmentSessionStatus::Ongoing, true],
        [AssessmentSessionStatus::Scheduled, AssessmentSessionStatus::Completed, true],
        [AssessmentSessionStatus::Scheduled, AssessmentSessionStatus::Cancelled, true],
        [AssessmentSessionStatus::Ongoing, AssessmentSessionStatus::Completed, true],
        [AssessmentSessionStatus::Ongoing, AssessmentSessionStatus::Cancelled, true],
        [AssessmentSessionStatus::Completed, AssessmentSessionStatus::Ongoing, false],
        [AssessmentSessionStatus::Cancelled, AssessmentSessionStatus::Scheduled, false],
    ];

    foreach ($transitions as [$from, $to, $expected]) {
        expect($to->allowsTransitionFrom($from))->toBe($expected);
    }
});

test('score status exposes a French label and a color for every case', function (): void {
    expect(ScoreStatus::cases())->toHaveCount(2);

    foreach (ScoreStatus::cases() as $status) {
        expect($status->label())->toBeString()->not->toBeEmpty()
            ->and($status->color())->toBeString()->not->toBeEmpty();
    }
});

test('only draft scores are mutable and publish is forward-only', function (): void {
    expect(ScoreStatus::Draft->isMutable())->toBeTrue()
        ->and(ScoreStatus::Published->isMutable())->toBeFalse()
        ->and(ScoreStatus::Published->allowsTransitionFrom(ScoreStatus::Draft))->toBeTrue()
        ->and(ScoreStatus::Draft->allowsTransitionFrom(ScoreStatus::Published))->toBeFalse();
});

test('score validation status classifies correctness', function (): void {
    expect(ScoreValidationStatus::Valid->isInvalid())->toBeFalse()
        ->and(ScoreValidationStatus::BelowPassing->isInvalid())->toBeFalse()
        ->and(ScoreValidationStatus::AboveMaximum->isInvalid())->toBeTrue()
        ->and(ScoreValidationStatus::BelowZero->isInvalid())->toBeTrue();
});

test('score validation status exposes a French label and a color for every case', function (): void {
    foreach (ScoreValidationStatus::cases() as $status) {
        expect($status->label())->toBeString()->not->toBeEmpty()
            ->and($status->color())->toBeString()->not->toBeEmpty();
    }
});

test('assessment type enumerates the twelve canonical types', function (): void {
    expect(AssessmentType::cases())->toHaveCount(12);

    foreach (AssessmentType::cases() as $type) {
        expect($type->label())->toBeString()->not->toBeEmpty()
            ->and($type->color())->toBeString()->not->toBeEmpty();
    }
});

test('assessment type options map every canonical type to its French label', function (): void {
    $options = AssessmentType::options();

    expect($options)->toBeArray()
        ->toHaveCount(12)
        ->and(array_values($options))->toContain('Quiz', 'Devoir', 'Examen partiel', 'Examen final');
});

test('only the custom type is free-form', function (): void {
    expect(AssessmentType::Custom->isCustom())->toBeTrue()
        ->and(AssessmentType::Quiz->isCustom())->toBeFalse()
        ->and(AssessmentType::FinalExam->isCustom())->toBeFalse();
});
