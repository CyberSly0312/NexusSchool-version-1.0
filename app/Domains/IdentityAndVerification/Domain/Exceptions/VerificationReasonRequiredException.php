<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Domain\Exceptions;

final class VerificationReasonRequiredException extends VerificationIdentityException
{
    public static function forTransition(string $transition): self
    {
        return new self(sprintf(
            'A meaningful reason is required to %s a verification identity.',
            $transition,
        ));
    }
}
