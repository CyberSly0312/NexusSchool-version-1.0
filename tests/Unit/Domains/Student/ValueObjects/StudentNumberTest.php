<?php

declare(strict_types=1);

use App\Domains\Student\Domain\ValueObjects\StudentNumber;

test('student number is normalized to uppercase', function (): void {
    expect((new StudentNumber('stu-2026-0001'))->value())->toBe('STU-2026-0001');
});

test('student number is trimmed', function (): void {
    expect((new StudentNumber('  A-123  '))->value())->toBe('A-123');
});

test('student number must not be empty', function (): void {
    new StudentNumber('   ');
})->throws(InvalidArgumentException::class);

test('student number must not exceed 50 characters', function (): void {
    new StudentNumber(str_repeat('A', 51));
})->throws(InvalidArgumentException::class);

test('student numbers are comparable', function (): void {
    $a = new StudentNumber('STU-1');
    $b = new StudentNumber('stu-1');
    $c = new StudentNumber('STU-2');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
