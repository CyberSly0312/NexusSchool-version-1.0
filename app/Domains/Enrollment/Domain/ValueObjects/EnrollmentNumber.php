<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * A validated, school-scoped enrollment number in the format
 * "ENR-{SCHOOL_PREFIX}-{SEQUENCE}". Immutable and never reused within a school
 * (enrollments are soft-deleted only, so count-based sequences are safe).
 */
final readonly class EnrollmentNumber
{
    private const PATTERN = '/^ENR-[A-F0-9]{6}-[0-9]{4,}$/';

    public function __construct(private string $value)
    {
        if (preg_match(self::PATTERN, $value) !== 1) {
            throw new InvalidArgumentException(
                "The enrollment number {$value} does not match the ENR-XXXXXX-NNNN format.",
            );
        }
    }

    public static function from(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
