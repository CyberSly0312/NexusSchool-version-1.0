<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\Exceptions;

use RuntimeException;

/**
 * Raised when an action is attempted on a student whose current status makes
 * it invalid (e.g. graduating an archived student).
 */
final class InvalidStudentStateException extends RuntimeException
{
    public static function for(string $action, string $currentStatus, string $reason): self
    {
        return new self(
            "Cannot {$action} a student currently in the '{$currentStatus}' state. {$reason}",
        );
    }
}
