<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Exceptions;

/**
 * Thrown when a publication targets a channel the school has not enabled or
 * that the engine does not implement yet (e.g. a future Email / SMS / Push).
 */
final class PublicationChannelUnavailableException extends AcademicCommunicationException
{
    public static function forChannel(string $channel, ?string $schoolId = null): self
    {
        return new self(
            sprintf(
                'Publication channel [%s] is unavailable%s.',
                $channel,
                $schoolId !== null ? " for school [{$schoolId}]" : '',
            ),
        );
    }

    public static function notImplemented(string $channel): self
    {
        return new self(sprintf('Publication channel [%s] is not implemented yet.', $channel));
    }
}
