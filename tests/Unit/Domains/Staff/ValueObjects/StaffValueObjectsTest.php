<?php

declare(strict_types=1);

use App\Domains\Staff\Domain\ValueObjects\BankAccount;
use App\Domains\Staff\Domain\ValueObjects\ContractPeriod;
use App\Domains\Staff\Domain\ValueObjects\EmailAddress;
use App\Domains\Staff\Domain\ValueObjects\NationalIdentifier;
use App\Domains\Staff\Domain\ValueObjects\PhoneNumber;
use App\Domains\Staff\Domain\ValueObjects\SalaryScale;
use App\Domains\Staff\Domain\ValueObjects\StaffNumber;

test('staff number trims and accepts the canonical format', function (): void {
    expect((new StaffNumber('  STF-A1B2C3-0042  '))->value())->toBe('STF-A1B2C3-0042');
});

test('staff number rejects empty values', function (): void {
    new StaffNumber('   ');
})->throws(InvalidArgumentException::class);

test('staff number rejects malformed values', function (string $value): void {
    new StaffNumber($value);
})->with([
    'wrong prefix' => ['GUA-A1B2C3-0001'],
    'no sequence' => ['STF-A1B2C3'],
    'short sequence' => ['STF-A1B2C3-001'],
    'lowercase hex' => ['STF-a1b2c3-0001'],
])->throws(InvalidArgumentException::class);

test('phone number accepts an international format', function (): void {
    expect((new PhoneNumber('  +226 70 11 22 33  '))->value())->toBe('+226 70 11 22 33');
});

test('phone number rejects empty values', function (): void {
    new PhoneNumber('   ');
})->throws(InvalidArgumentException::class);

test('phone number rejects clearly invalid formats', function (string $value): void {
    new PhoneNumber($value);
})->with([
    'letters' => ['not-a-number'],
    'too short' => ['7'],
    'unsupported symbols' => ['+226***701***123'],
])->throws(InvalidArgumentException::class);

test('email address is normalized to lowercase', function (): void {
    expect((new EmailAddress('  Staff@Example.COM  '))->value())->toBe('staff@example.com');
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

test('national identifier trims and stores alphanumerics', function (): void {
    expect((new NationalIdentifier('  CIN2025AB12  '))->value())->toBe('CIN2025AB12');
});

test('national identifier rejects empty values', function (): void {
    new NationalIdentifier('   ');
})->throws(InvalidArgumentException::class);

test('national identifier rejects overlong values', function (): void {
    new NationalIdentifier(str_repeat('A', 51));
})->throws(InvalidArgumentException::class);

test('bank account accepts an IBAN-like value', function (): void {
    expect((new BankAccount('BF23000012345678'))->value())->toBe('BF23000012345678');
});

test('bank account rejects malformed values', function (): void {
    new BankAccount('not-an-iban');
})->throws(InvalidArgumentException::class);

test('salary scale uppercases and validates the format', function (): void {
    expect((new SalaryScale('s15'))->value())->toBe('S15');
});

test('salary scale rejects malformed values', function (): void {
    new SalaryScale('15');
})->throws(InvalidArgumentException::class);

test('contract period accepts an open-ended contract', function (): void {
    $period = new ContractPeriod('2026-01-01');

    expect($period->isCurrent('2026-01-01'))->toBeTrue()
        ->and($period->isCurrent('2030-01-01'))->toBeTrue();
});

test('contract period bounds the validity window', function (): void {
    $period = new ContractPeriod('2026-01-01', '2026-12-31');

    expect($period->isCurrent('2025-12-31'))->toBeFalse()
        ->and($period->isCurrent('2026-01-01'))->toBeTrue()
        ->and($period->isCurrent('2026-12-31'))->toBeTrue()
        ->and($period->isCurrent('2027-01-01'))->toBeFalse();
});

test('contract period rejects an end date before the start date', function (): void {
    new ContractPeriod('2027-01-01', '2026-01-01');
})->throws(InvalidArgumentException::class);

test('contract period rejects malformed dates', function (): void {
    new ContractPeriod('2026/01/01');
})->throws(InvalidArgumentException::class);
