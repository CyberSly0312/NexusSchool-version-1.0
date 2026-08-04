<?php

declare(strict_types=1);

use App\Domains\Guardian\Domain\ValueObjects\EmailAddress;
use App\Domains\Guardian\Domain\ValueObjects\GuardianName;
use App\Domains\Guardian\Domain\ValueObjects\PhoneNumber;
use App\Domains\Guardian\Domain\ValueObjects\PickupWindow;

test('phone number accepts an international format', function (): void {
    expect((new PhoneNumber('  +237 690 123 456  '))->value())->toBe('+237 690 123 456');
});

test('phone number rejects empty values', function (): void {
    new PhoneNumber('   ');
})->throws(InvalidArgumentException::class);

test('phone number rejects clearly invalid formats', function (string $value): void {
    new PhoneNumber($value);
})->with([
    'letters' => ['not-a-number'],
    'too short' => ['7'],
    'unsupported symbols' => ['+237***690***123'],
])->throws(InvalidArgumentException::class);

test('email address is normalized to lowercase', function (): void {
    expect((new EmailAddress('  Guardian@Example.COM  '))->value())->toBe('guardian@example.com');
});

test('email address rejects empty values', function (): void {
    new EmailAddress('   ');
})->throws(InvalidArgumentException::class);

test('email address rejects malformed values', function (): void {
    new EmailAddress('not-an-email');
})->throws(InvalidArgumentException::class);

test('email address rejects overlong values', function (): void {
    new EmailAddress(str_repeat('a', 244).'@example.com');
})->throws(InvalidArgumentException::class);

test('pickup window accepts an open-ended grant', function (): void {
    $window = new PickupWindow();

    expect($window->validFrom)->toBeNull()
        ->and($window->validUntil)->toBeNull()
        ->and($window->isValidAt('2026-08-03'))->toBeTrue();
});

test('pickup window bounds the validity period', function (): void {
    $window = new PickupWindow(validFrom: '2026-09-01', validUntil: '2027-06-30');

    expect($window->isValidAt('2026-08-31'))->toBeFalse()
        ->and($window->isValidAt('2026-09-01'))->toBeTrue()
        ->and($window->isValidAt('2027-06-30'))->toBeTrue()
        ->and($window->isValidAt('2027-07-01'))->toBeFalse();
});

test('pickup window rejects a start date after the end date', function (): void {
    new PickupWindow(validFrom: '2027-01-01', validUntil: '2026-01-01');
})->throws(InvalidArgumentException::class);

test('pickup window rejects malformed dates', function (): void {
    new PickupWindow(validFrom: '2026/01/01');
})->throws(InvalidArgumentException::class);

test('guardian name trims and builds a full name', function (): void {
    $name = GuardianName::from('  Marie  ', '  Dupont  ');

    expect($name->fullName())->toBe('Marie Dupont')
        ->and($name->displayName())->toBe('Marie Dupont');
});

test('guardian name prefers the preferred name in the display name', function (): void {
    $name = GuardianName::from('Marie', 'Dupont', preferredName: 'Mimi');

    expect($name->displayName())->toBe('Mimi Dupont');
});

test('guardian name rejects an empty first name', function (): void {
    GuardianName::from('   ', 'Dupont');
})->throws(InvalidArgumentException::class);

test('guardian name rejects an empty last name', function (): void {
    GuardianName::from('Marie', '   ');
})->throws(InvalidArgumentException::class);
