<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\DTOs;

/**
 * Outcome of one channel delivery (SPR-012). Failure keeps the PublicationRecord
 * retryable (a new attempt, never a mutation of a completed record).
 */
final readonly class PublicationDeliveryOutcome
{
    public function __construct(
        public bool $success,
        public ?string $error = null,
        public array $metadata = [],
    ) {
    }

    public static function success(array $metadata = []): self
    {
        return new self(true, null, $metadata);
    }

    public static function failure(string $error, array $metadata = []): self
    {
        return new self(false, $error, $metadata);
    }
}
