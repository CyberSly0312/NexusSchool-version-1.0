<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Exceptions;

/**
 * Thrown when an approval is recorded on a document whose publication policy
 * does not require one, or when a role that is not part of the policy's
 * approval roles attempts to approve the document.
 */
final class DocumentApprovalException extends AcademicCommunicationException
{
    public static function notRequired(int|string $documentId): self
    {
        return new self(sprintf('Document [#%s] does not require approval.', $documentId));
    }

    public static function invalidRole(int|string $documentId, string $role): self
    {
        return new self(sprintf('Role [%s] is not allowed to approve document [#%s].', $role, $documentId));
    }
}
