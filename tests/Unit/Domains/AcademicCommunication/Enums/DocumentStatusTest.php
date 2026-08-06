<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;

test('the document lifecycle is forward-only', function (): void {
    expect(DocumentStatus::Generated->allowsTransitionFrom(DocumentStatus::Draft))->toBeTrue()
        ->and(DocumentStatus::Reviewed->allowsTransitionFrom(DocumentStatus::Generated))->toBeTrue()
        ->and(DocumentStatus::Approved->allowsTransitionFrom(DocumentStatus::Reviewed))->toBeTrue()
        ->and(DocumentStatus::Published->allowsTransitionFrom(DocumentStatus::Approved))->toBeTrue()
        ->and(DocumentStatus::Archived->allowsTransitionFrom(DocumentStatus::Published))->toBeTrue();
});

test('published can also be reached straight from generated', function (): void {
    expect(DocumentStatus::Published->allowsTransitionFrom(DocumentStatus::Generated))->toBeTrue();
});

test('documents cannot move backwards or skip draft', function (): void {
    expect(DocumentStatus::Draft->allowsTransitionFrom(DocumentStatus::Generated))->toBeFalse()
        ->and(DocumentStatus::Generated->allowsTransitionFrom(DocumentStatus::Reviewed))->toBeFalse()
        ->and(DocumentStatus::Reviewed->allowsTransitionFrom(DocumentStatus::Draft))->toBeFalse()
        ->and(DocumentStatus::Approved->allowsTransitionFrom(DocumentStatus::Draft))->toBeFalse();
});

test('published and archived versions are immutable', function (): void {
    expect(DocumentStatus::Published->isImmutable())->toBeTrue()
        ->and(DocumentStatus::Archived->isImmutable())->toBeTrue()
        ->and(DocumentStatus::Generated->isImmutable())->toBeFalse();
});
