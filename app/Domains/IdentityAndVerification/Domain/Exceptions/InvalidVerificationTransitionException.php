<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Domain\Exceptions;

use App\Domains\IdentityAndVerification\Domain\Enums\VerificationStatus;

final class InvalidVerificationTransitionException extends VerificationIdentityException
{
    public static function fromTo(VerificationStatus $from, VerificationStatus $to): self
    {
        return new self(sprintf(
            'Verification identity cannot transition from [%s] to [%s].',
            $from->value,
            $to->value,
        ));
    }

    public static function selfReplacement(int|string $documentId): self
    {
        return new self(sprintf(
            'Document [%s] cannot be replaced by itself.',
            (string) $documentId,
        ));
    }
}
