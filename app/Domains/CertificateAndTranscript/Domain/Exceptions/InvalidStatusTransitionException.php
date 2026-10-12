<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Exceptions;

use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestStatus;

/**
 * Thrown when a completion request is moved to a status the lifecycle does not
 * allow from its current one (SPR-015).
 */
final class InvalidStatusTransitionException extends CompletionRequestException
{
    public static function from(CompletionRequestStatus $from, CompletionRequestStatus $to): self
    {
        return new self("Cannot transition completion request status from [{$from->value}] to [{$to->value}].");
    }
}
