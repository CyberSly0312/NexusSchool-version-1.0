<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\Exceptions;

use RuntimeException;

/**
 * Raised when a student number or email is already taken within the same
 * organization (school). Mirrors the SPR-002 duplicate resource convention.
 */
final class DuplicateStudentException extends RuntimeException
{
    public static function studentNumber(string $number): self
    {
        return new self("A student with the number {$number} already exists in this school.");
    }

    public static function email(string $email): self
    {
        return new self("A student with the email {$email} already exists in this school.");
    }

    public static function message(string $message): self
    {
        return new self($message);
    }
}
