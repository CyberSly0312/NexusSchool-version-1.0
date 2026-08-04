<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Domain\Exceptions;

use RuntimeException;

/**
 * Raised when a student already has an active enrollment in the same academic
 * year, or when the enrollment number is already taken in the school.
 */
final class DuplicateEnrollmentException extends RuntimeException
{
    public static function activeEnrollment(int $studentId, int $academicYearId): self
    {
        return new self(
            "The student #{$studentId} already has an active enrollment in the academic year #{$academicYearId}.",
        );
    }

    public static function enrollmentNumber(string $number): self
    {
        return new self("An enrollment with the number {$number} already exists in this school.");
    }

    public static function message(string $message): self
    {
        return new self($message);
    }
}
