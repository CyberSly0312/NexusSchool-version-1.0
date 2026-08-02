<?php

declare(strict_types=1);

use App\Domains\Academic\Domain\ValueObjects\Coefficient;

test('accepts a positive coefficient', function (): void {
    $coefficient = new Coefficient(5.0);

    expect($coefficient->value())->toBe(5.0)
        ->and($coefficient->isOptional())->toBeFalse();
});

test('marks optional coefficients', function (): void {
    $coefficient = new Coefficient(1.0, true);

    expect($coefficient->isOptional())->toBeTrue();
});

test('rejects a non-positive coefficient', function (): void {
    new Coefficient(0.0);
})->throws(InvalidArgumentException::class);

test('rejects a negative coefficient', function (): void {
    new Coefficient(-2.5);
})->throws(InvalidArgumentException::class);
