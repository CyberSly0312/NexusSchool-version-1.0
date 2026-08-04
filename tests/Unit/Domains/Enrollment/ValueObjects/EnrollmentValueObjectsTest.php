<?php

declare(strict_types=1);

use App\Domains\Enrollment\Domain\ValueObjects\AcademicResult;
use App\Domains\Enrollment\Domain\ValueObjects\EnrollmentNumber;
use App\Domains\Enrollment\Domain\ValueObjects\TransferReason;

test('enrollment number accepts the canonical format', function (): void {
    expect((new EnrollmentNumber('ENR-ABCDEF-0001'))->value())->toBe('ENR-ABCDEF-0001')
        ->and(EnrollmentNumber::from('ENR-123456-0042')->value())->toBe('ENR-123456-0042');
});

test('enrollment number rejects malformed values', function (string $value): void {
    new EnrollmentNumber($value);
})->with([
    'missing prefix' => ['STU-ABCDEF-0001'],
    'short sequence' => ['ENR-ABCDEF-1'],
    'lowercase hex' => ['enr-abcdef-0001'],
    'wrong separator' => ['ENR/ABCDEF/0001'],
    'empty' => [''],
])->throws(InvalidArgumentException::class);

test('enrollment numbers are comparable', function (): void {
    $a = new EnrollmentNumber('ENR-ABCDEF-0001');
    $b = new EnrollmentNumber('ENR-ABCDEF-0001');
    $c = new EnrollmentNumber('ENR-ABCDEF-0002');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse()
        ->and((string) $a)->toBe('ENR-ABCDEF-0001');
});

test('academic result validates its average', function (): void {
    expect((new AcademicResult(12.5))->average)->toBe(12.5);

    new AcademicResult(-0.1);
})->throws(InvalidArgumentException::class);

test('academic result rejects averages above 100', function (): void {
    new AcademicResult(100.1);
})->throws(InvalidArgumentException::class);

test('academic result rejects an invalid rank', function (): void {
    new AcademicResult(12.5, rank: 0);
})->throws(InvalidArgumentException::class);

test('academic result rejects out of range marks', function (): void {
    new AcademicResult(12.5, highest: 101);
})->throws(InvalidArgumentException::class);

test('academic result round-trips through fromArray and toArray', function (): void {
    $result = AcademicResult::fromArray([
        'average' => '13.75',
        'rank' => '4',
        'highest' => '18.5',
        'lowest' => '8.25',
    ]);

    expect($result->toArray())->toBe([
        'average' => 13.75,
        'rank' => 4,
        'highest' => 18.5,
        'lowest' => 8.25,
    ]);
});

test('transfer reason is trimmed', function (): void {
    expect((new TransferReason('  Déménagement familial  '))->value())->toBe('Déménagement familial');
});

test('transfer reason must not be empty', function (): void {
    new TransferReason('   ');
})->throws(InvalidArgumentException::class);

test('transfer reason must not exceed 255 characters', function (): void {
    new TransferReason(str_repeat('a', 256));
})->throws(InvalidArgumentException::class);
