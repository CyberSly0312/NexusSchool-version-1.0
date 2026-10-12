<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Exceptions;

/**
 * Thrown when the required published evidence cannot be produced: an
 * unpublished council session or a pending report-card term. Per ADR-021 the
 * engine consumes only published / immutable evidence and must refuse issuance
 * otherwise (SPR-015).
 */
final class UnpublishedEvidenceException extends CompletionRequestException
{
    public static function for(string $evidence, int|string $studentId): self
    {
        return new self("Refusing issuance: required evidence [{$evidence}] is not published for student [{$studentId}].");
    }
}
