<?php

declare(strict_types=1);

use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\Enums\TransferStatus;

test('student status cases map to the legacy lifecycle', function (): void {
    expect(StudentStatus::cases())
        ->each->toBeInstanceOf(StudentStatus::class);
});

test('student status has a label and color', function (): void {
    expect(StudentStatus::Active->label())->toBe('Actif')
        ->and(StudentStatus::Active->color())->toBe('success');
});

test('only archived status is immutable', function (): void {
    expect(StudentStatus::Archived->isImmutable())->toBeTrue()
        ->and(StudentStatus::Active->isImmutable())->toBeFalse();
});

test('only active status is considered active', function (): void {
    expect(StudentStatus::Active->isActive())->toBeTrue()
        ->and(StudentStatus::Suspended->isActive())->toBeFalse();
});

test('transfer status knows when it is active', function (): void {
    expect(TransferStatus::Incoming->isActive())->toBeTrue()
        ->and(TransferStatus::None->isActive())->toBeFalse();
});
