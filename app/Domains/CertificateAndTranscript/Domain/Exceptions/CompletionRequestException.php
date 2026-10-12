<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Exceptions;

use RuntimeException;

/**
 * Base exception of the Certificate & Transcript engine (SPR-015).
 */
class CompletionRequestException extends RuntimeException
{
}
