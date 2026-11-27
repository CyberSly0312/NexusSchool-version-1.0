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

    /**
     * Internal reason: family-panel activation is deferred to Task 11.3.
     */
    public static function familyActivationBlocked(): self
    {
        return new self('L’activation des comptes élèves et parents est différée jusqu’à la mise en place de l’autorisation par élève.');
    }
}
