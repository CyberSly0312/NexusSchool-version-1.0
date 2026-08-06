<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Exceptions;

/**
 * Thrown when a publication attempt completes with zero successful deliveries
 * (e.g. every channel failed or every target channel was unavailable).
 */
final class DocumentPublicationFailedException extends AcademicCommunicationException
{
    public static function noDelivery(string $documentNumber): self
    {
        return new self(sprintf('Document [%s] could not be published: no delivery succeeded.', $documentNumber));
    }
}
