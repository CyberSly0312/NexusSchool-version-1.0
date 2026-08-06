<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Exceptions;

use RuntimeException;

/**
 * Raised when a staff number, email, phone or national identifier is already
 * taken within the same organization (school).
 */
final class DuplicateStaffException extends RuntimeException
{
    public static function staffNumber(string $number): self
    {
        return new self("A staff member with the number {$number} already exists in this school.");
    }

    public static function email(string $email): self
    {
        return new self("A staff member with the email {$email} already exists in this school.");
    }

    public static function phone(string $phone): self
    {
        return new self("A staff member with the phone number {$phone} already exists in this school.");
    }

    public static function nationalId(string $nationalId): self
    {
        return new self("A staff member with the national identifier {$nationalId} already exists in this school.");
    }

    public static function message(string $message): self
    {
        return new self($message);
    }
}
