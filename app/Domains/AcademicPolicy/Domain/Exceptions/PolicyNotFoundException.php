<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Domain\Exceptions;

use RuntimeException;

final class PolicyNotFoundException extends RuntimeException
{
    public static function forId(string $schoolId, string $policyId): self
    {
        return new self(
            sprintf('Academic policy [%s] not found in school [%s].', $policyId, $schoolId),
        );
    }
}
