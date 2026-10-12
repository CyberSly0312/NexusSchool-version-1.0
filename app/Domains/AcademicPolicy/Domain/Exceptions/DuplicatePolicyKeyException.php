<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Domain\Exceptions;

use RuntimeException;

final class DuplicatePolicyKeyException extends RuntimeException
{
    public static function forSchool(string $schoolId, string $key): self
    {
        return new self(
            sprintf('An academic policy [%s] already exists for school [%s].', $key, $schoolId),
        );
    }
}
