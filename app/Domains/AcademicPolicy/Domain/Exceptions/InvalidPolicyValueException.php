<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Domain\Exceptions;

use RuntimeException;

final class InvalidPolicyValueException extends RuntimeException
{
    public static function forType(string $expected, mixed $actual): self
    {
        return new self(
            sprintf(
                'Invalid academic policy value: expected %s, got %s.',
                $expected,
                get_debug_type($actual),
            ),
        );
    }
}
