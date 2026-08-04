<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * A validated transfer reason. Kept as a dedicated value object so the policy
 * around the free-text reason (non-empty, bounded length) lives in one place.
 */
final readonly class TransferReason
{
    private const MAX_LENGTH = 255;

    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new InvalidArgumentException('The transfer reason cannot be empty.');
        }

        if (mb_strlen($trimmed) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('The transfer reason cannot exceed '.self::MAX_LENGTH.' characters.');
        }

        $this->value = $trimmed;
    }

    public static function from(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
