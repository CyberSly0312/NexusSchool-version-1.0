<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Domain\ValueObjects;

use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;
use App\Domains\AcademicPolicy\Domain\Exceptions\InvalidPolicyValueException;

/**
 * Typed, validated value of an academic policy (SPR-009.5).
 *
 * Guarantees that a stored policy value matches its declared type: a boolean
 * policy never yields a string, an integer policy never yields a float with a
 * fractional part, and so on. Values are kept as plain PHP primitives and
 * serialized to JSON by the persistence layer.
 */
final readonly class PolicyValue
{
    public function __construct(
        public AcademicPolicyValueType $type,
        public mixed $value,
    ) {
        $this->assertValid();
    }

    public function asRaw(): mixed
    {
        return $this->value;
    }

    /**
     * Builds a value from a decoded JSON storage round-trip.
     *
     * JSON encodes whole floats as integers (20.0 becomes 20), so the declared
     * type is reapplied on read: a float policy always yields a float, an
     * integer policy always yields an int, a boolean always a bool.
     */
    public static function fromStored(AcademicPolicyValueType $type, mixed $value): self
    {
        $normalized = match ($type) {
            AcademicPolicyValueType::Float => is_int($value) ? (float) $value : $value,
            AcademicPolicyValueType::Integer => is_float($value) ? (int) $value : $value,
            AcademicPolicyValueType::Boolean => $value === 1 ? true : ($value === 0 ? false : $value),
            default => $value,
        };

        return new self($type, $normalized);
    }

    /**
     * Coerces a raw input (typically from a form) onto the declared type.
     *
     * Coercion is strict: only unambiguous conversions are accepted. A value
     * that cannot be coerced produces an {@see InvalidPolicyValueException}
     * instead of being silently cast.
     */
    public static function coerce(AcademicPolicyValueType $type, mixed $raw): self
    {
        return match ($type) {
            AcademicPolicyValueType::String => is_string($raw)
                ? new self($type, $raw)
                : throw InvalidPolicyValueException::forType($type->value, $raw),

            AcademicPolicyValueType::Integer => is_int($raw)
                ? new self($type, $raw)
                : (is_string($raw) && preg_match('/^-?\d+$/', $raw) === 1
                    ? new self($type, (int) $raw)
                    : throw InvalidPolicyValueException::forType($type->value, $raw)),

            AcademicPolicyValueType::Float => is_float($raw) || is_int($raw)
                ? new self($type, (float) $raw)
                : (is_string($raw) && is_numeric($raw)
                    ? new self($type, (float) $raw)
                    : throw InvalidPolicyValueException::forType($type->value, $raw)),

            AcademicPolicyValueType::Boolean => is_bool($raw)
                ? new self($type, $raw)
                : (is_string($raw) && in_array($raw, ['1', '0', 'true', 'false'], true)
                    ? new self($type, $raw === '1' || $raw === 'true')
                    : throw InvalidPolicyValueException::forType($type->value, $raw)),

            AcademicPolicyValueType::Json => is_array($raw)
                ? new self($type, $raw)
                : (is_string($raw)
                    ? new self($type, self::decodeJson($raw))
                    : throw InvalidPolicyValueException::forType($type->value, $raw)),
        };
    }

    /**
     * @return array<mixed>
     */
    private static function decodeJson(string $raw): array
    {
        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            throw new InvalidPolicyValueException('A json policy value must decode to an array.');
        }

        return $decoded;
    }

    private function assertValid(): void
    {
        if ($this->type === AcademicPolicyValueType::Json) {
            if (! is_array($this->value)) {
                throw new InvalidPolicyValueException('A json policy value must be an array.');
            }

            return;
        }

        $valid = match ($this->type) {
            AcademicPolicyValueType::String => is_string($this->value),
            AcademicPolicyValueType::Integer => is_int($this->value),
            AcademicPolicyValueType::Float => is_float($this->value),
            default => is_bool($this->value),
        };

        if (! $valid) {
            throw new InvalidPolicyValueException(
                sprintf('A %s policy value must be a %s.', $this->type->value, $this->type->value),
            );
        }
    }
}
