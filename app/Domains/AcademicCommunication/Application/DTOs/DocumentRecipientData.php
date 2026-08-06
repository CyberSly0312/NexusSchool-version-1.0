<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\DTOs;

use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;

/**
 * A resolved recipient of a document publication (SPR-012).
 *
 * The dispatcher builds recipients from the policy's scopes: administrative
 * scopes resolve to staff users, self-scoped scopes (parent, student) resolve
 * to the concrete person so they can only ever receive their own data. This
 * DTO is model-free; the concrete recipient is referenced by type + id and the
 * optional user id used by the portal channels to grant access.
 */
final readonly class DocumentRecipientData
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $recipientType,
        public int|string|null $recipientId,
        public string $recipientLabel,
        public VisibilityScope $visibilityScope,
        public int|string|null $userId = null,
        public array $metadata = [],
    ) {
    }
}
