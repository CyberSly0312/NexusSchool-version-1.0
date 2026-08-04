<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Exceptions;

use RuntimeException;

/**
 * Raised when a guardian number, email or phone is already taken within the
 * same organization (school).
 */
final class DuplicateGuardianException extends RuntimeException
{
    public static function guardianNumber(string $number): self
    {
        return new self("A guardian with the number {$number} already exists in this school.");
    }

    public static function email(string $email): self
    {
        return new self("A guardian with the email {$email} already exists in this school.");
    }

    public static function phone(string $phone): self
    {
        return new self("A guardian with the phone number {$phone} already exists in this school.");
    }

    public static function message(string $message): self
    {
        return new self($message);
    }
}
