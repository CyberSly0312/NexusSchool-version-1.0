<?php

declare(strict_types=1);

use App\Domains\Student\Domain\ValueObjects\BirthDate;
use App\Domains\Student\Domain\ValueObjects\EmailAddress;
use App\Domains\Student\Domain\ValueObjects\GuardianReference;
use App\Domains\Student\Domain\ValueObjects\PhoneNumber;
use App\Domains\Student\Domain\ValueObjects\StudentName;

test('student name builds a full name', function (): void {
    $name = new StudentName('Jean', 'Dupont', 'Marc');

    expect($name->fullName())->toBe('Jean Marc Dupont');
});

test('student name requires first and last name', function (): void {
    new StudentName('', 'Dupont');
})->throws(InvalidArgumentException::class);

test('student name uses the preferred name for display', function (): void {
    $name = new StudentName('Jean', 'Dupont', preferredName: 'Johnny');

    expect($name->displayName())->toBe('Johnny Dupont');
});

test('birth date cannot be in the future', function (): void {
    BirthDate::from('2099-01-01');
})->throws(InvalidArgumentException::class);

test('birth date computes age', function (): void {
    $birthDate = BirthDate::from(now()->subYears(12)->toDateString());

    expect($birthDate->age())->toBe(12);
});

test('email is normalized and validated', function (): void {
    expect((new EmailAddress('  JOHN@Example.COM '))->value())->toBe('john@example.com');
});

test('email must be valid', function (): void {
    new EmailAddress('not-an-email');
})->throws(InvalidArgumentException::class);

test('phone number accepts international formats', function (): void {
    expect((new PhoneNumber('+225 07 08 09 10 11'))->value())->toBe('+225 07 08 09 10 11');
});

test('phone number rejects invalid values', function (): void {
    new PhoneNumber('abc');
})->throws(InvalidArgumentException::class);

test('guardian reference validates relationship', function (): void {
    new GuardianReference('M. Kouassi', '+2250000000000', null, 'cousin');
})->throws(InvalidArgumentException::class);

test('guardian reference builds from a raw form row', function (): void {
    $guardian = GuardianReference::fromNullableGuardian([
        'name' => 'Mme Aya',
        'phone' => '+2251111111111',
        'relationship' => 'parent',
        'is_primary' => true,
    ]);

    expect($guardian)->not->toBeNull()
        ->and($guardian->name)->toBe('Mme Aya')
        ->and($guardian->isPrimary)->toBeTrue();
});

test('empty guardian rows are skipped', function (): void {
    expect(GuardianReference::fromNullableGuardian(['name' => '']))->toBeNull();
});
