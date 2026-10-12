<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Exceptions;

/**
 * Thrown when a certificate is requested for a student the eligibility
 * orchestrator judged ineligible from the authoritative evidence (SPR-015).
 */
final class CertificateIneligibilityException extends CompletionRequestException
{
    public static function for(int|string $studentId, array $reasons): self
    {
        return new self(
            sprintf(
                'Student [%s] is not eligible for an end-of-study certificate: %s.',
                $studentId,
                implode('; ', $reasons),
            ),
        );
    }
}
