<?php

declare(strict_types=1);

use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;

test('a council session transitions forward through its lifecycle', function (): void {
    expect(CouncilSessionStatus::Draft->allowsTransitionFrom(CouncilSessionStatus::Draft))->toBeFalse()
        ->and(CouncilSessionStatus::Convened->allowsTransitionFrom(CouncilSessionStatus::Draft))->toBeTrue()
        ->and(CouncilSessionStatus::Convened->allowsTransitionFrom(CouncilSessionStatus::Convened))->toBeFalse()
        ->and(CouncilSessionStatus::Published->allowsTransitionFrom(CouncilSessionStatus::Convened))->toBeTrue()
        ->and(CouncilSessionStatus::Published->allowsTransitionFrom(CouncilSessionStatus::Draft))->toBeFalse()
        ->and(CouncilSessionStatus::Archived->allowsTransitionFrom(CouncilSessionStatus::Published))->toBeTrue()
        ->and(CouncilSessionStatus::Archived->allowsTransitionFrom(CouncilSessionStatus::Convened))->toBeFalse()
        ->and(CouncilSessionStatus::Draft->allowsTransitionFrom(CouncilSessionStatus::Published))->toBeFalse();
});

test('published and archived sessions are immutable', function (): void {
    expect(CouncilSessionStatus::Draft->isImmutable())->toBeFalse()
        ->and(CouncilSessionStatus::Convened->isImmutable())->toBeFalse()
        ->and(CouncilSessionStatus::Published->isImmutable())->toBeTrue()
        ->and(CouncilSessionStatus::Archived->isImmutable())->toBeTrue();
});

test('only convened sessions accept decisions', function (): void {
    expect(CouncilSessionStatus::Draft->isDeliberating())->toBeFalse()
        ->and(CouncilSessionStatus::Convened->isDeliberating())->toBeTrue()
        ->and(CouncilSessionStatus::Published->isDeliberating())->toBeFalse()
        ->and(CouncilSessionStatus::Archived->isDeliberating())->toBeFalse();
});

test('council session statuses carry french labels and colors', function (): void {
    expect(CouncilSessionStatus::Draft->label())->toBe('Brouillon')
        ->and(CouncilSessionStatus::Convened->label())->toBe('Réuni')
        ->and(CouncilSessionStatus::Published->label())->toBe('Publié')
        ->and(CouncilSessionStatus::Archived->label())->toBe('Archivé')
        ->and(CouncilSessionStatus::Published->color())->toBe('success');
});

test('decision types and categories carry french labels', function (): void {
    expect(\App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType::Passed->label())->toBe('Admis')
        ->and(\App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType::Failed->label())->toBe('Redoublement')
        ->and(\App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType::Reported->label())->toBe('Signalé')
        ->and(\App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionCategory::Promotion->label())->toBe('Promotion')
        ->and(\App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionCategory::Conduct->label())->toBe('Conduite');
});
