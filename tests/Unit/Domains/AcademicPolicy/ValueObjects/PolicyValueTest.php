<?php

declare(strict_types=1);

use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;
use App\Domains\AcademicPolicy\Domain\Exceptions\InvalidPolicyValueException;
use App\Domains\AcademicPolicy\Domain\ValueObjects\PolicyValue;

test('a policy value accepts its declared scalar type', function (): void {
    expect((new PolicyValue(AcademicPolicyValueType::String, 'réglement'))->asRaw())->toBe('réglement')
        ->and((new PolicyValue(AcademicPolicyValueType::Integer, 50))->asRaw())->toBe(50)
        ->and((new PolicyValue(AcademicPolicyValueType::Float, 50.5))->asRaw())->toBe(50.5)
        ->and((new PolicyValue(AcademicPolicyValueType::Boolean, true))->asRaw())->toBeTrue()
        ->and((new PolicyValue(AcademicPolicyValueType::Json, ['a' => 1]))->asRaw())->toBe(['a' => 1]);
});

test('a policy value rejects a value of the wrong type', function (string $type, mixed $value): void {
    expect(fn () => new PolicyValue(AcademicPolicyValueType::from($type), $value))
        ->toThrow(InvalidPolicyValueException::class);
})->with([
    'string rejects integer' => ['string', 42],
    'integer rejects float' => ['integer', 1.5],
    'integer rejects text' => ['integer', 'abc'],
    'float rejects text' => ['float', 'abc'],
    'boolean rejects integer' => ['boolean', 1],
    'json rejects scalar' => ['json', 'scalar'],
]);

test('coerce normalizes unambiguous raw inputs onto the declared type', function (): void {
    expect(PolicyValue::coerce(AcademicPolicyValueType::Integer, '42')->asRaw())->toBe(42)
        ->and(PolicyValue::coerce(AcademicPolicyValueType::Float, '50.5')->asRaw())->toBe(50.5)
        ->and(PolicyValue::coerce(AcademicPolicyValueType::Float, 50)->asRaw())->toBe(50.0)
        ->and(PolicyValue::coerce(AcademicPolicyValueType::Boolean, '1')->asRaw())->toBeTrue()
        ->and(PolicyValue::coerce(AcademicPolicyValueType::Boolean, 'false')->asRaw())->toBeFalse()
        ->and(PolicyValue::coerce(AcademicPolicyValueType::Json, '{"a":1}')->asRaw())->toBe(['a' => 1]);
});

test('coerce rejects ambiguous raw inputs', function (string $type, mixed $value): void {
    expect(fn () => PolicyValue::coerce(AcademicPolicyValueType::from($type), $value))
        ->toThrow(InvalidPolicyValueException::class);
})->with([
    'integer rejects non digit' => ['integer', '1.5'],
    'float rejects empty' => ['float', ''],
    'boolean rejects arbitrary text' => ['boolean', 'yes'],
    'json rejects invalid json' => ['json', '{not json}'],
]);
