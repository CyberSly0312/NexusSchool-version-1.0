<?php

declare(strict_types=1);

namespace App\Domains\User\Exceptions;

use RuntimeException;

final class AccountInvitationActivationException extends RuntimeException
{
    public static function invalidInvitation(): self
    {
        return new self('The invitation is invalid, expired, or has already been used.');
    }
}
