<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\Exceptions;

use RuntimeException;

/**
 * Raised when the requested academic assignment (academic year / class) is not
 * available for the school the student belongs to, or is no longer active.
 */
final class AcademicAssignmentException extends RuntimeException
{
    public static function missingYear(string $schoolId): self
    {
        return new self("No active academic year could be found for school {$schoolId}.");
    }

    public static function inactiveYear(string $yearName): self
    {
        return new self("The academic year {$yearName} is not active.");
    }

    public static function classMismatch(string $classId): self
    {
        return new self("The class {$classId} does not belong to this school or its active academic year.");
    }
}
