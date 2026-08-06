<?php

declare(strict_types=1);

use App\Domains\Staff\Domain\Enums\StaffSpecialization;
use App\Domains\Staff\Domain\Enums\StaffStatus;

test('staff status exposes french labels', function (): void {
    expect(StaffStatus::Active->label())->toBe('Actif')
        ->and(StaffStatus::Suspended->label())->toBe('Suspendu')
        ->and(StaffStatus::Terminated->label())->toBe('Licencié');
});

test('staff status flags immutable states', function (): void {
    expect(StaffStatus::Retired->isImmutable())->toBeTrue()
        ->and(StaffStatus::Resigned->isImmutable())->toBeTrue()
        ->and(StaffStatus::Terminated->isImmutable())->toBeTrue()
        ->and(StaffStatus::Active->isImmutable())->toBeFalse()
        ->and(StaffStatus::Suspended->isImmutable())->toBeFalse();
});

test('teacher is recognized as the teaching specialization', function (): void {
    expect(StaffSpecialization::Teacher->isTeacher())->toBeTrue()
        ->and(StaffSpecialization::Director->isTeacher())->toBeFalse();
});

test('staff specializations cover the canonical workforce roles', function (): void {
    expect(StaffSpecialization::cases())->toHaveCount(8);
});
