<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Domain\Exceptions;

use RuntimeException;

final class InvalidPolicyScopeException extends RuntimeException
{
    public static function forScope(string $scope): self
    {
        return new self(
            sprintf('Invalid academic policy scope [%s]: the scope reference is missing or inconsistent.', $scope),
        );
    }
}
