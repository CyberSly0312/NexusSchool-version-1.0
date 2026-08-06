<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\DTOs;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;

/**
 * Snapshot handed to a publication channel driver (SPR-012).
 *
 * Carries the version to deliver, the concrete recipient (only self-scoped data
 * for parents/students) and the channel. Drivers must never re-derive business
 * data — they receive the exact payload resolved by the dispatcher, including
 * the pre-rendered, scope-redacted HTML for the portal channels.
 */
final readonly class PublicationPayload
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $schoolId,
        public int|string $documentId,
        public int|string $versionId,
        public int|string|null $templateId,
        public DocumentChannel $channel,
        public string $recipientType,
        public int|string|null $recipientId,
        public string $recipientLabel,
        public VisibilityScope $visibilityScope,
        public ?string $html = null,
        public array $metadata = [],
    ) {
    }
}
