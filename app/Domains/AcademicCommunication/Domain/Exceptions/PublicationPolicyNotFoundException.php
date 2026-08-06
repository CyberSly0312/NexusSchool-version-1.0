<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Exceptions;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;

/**
 * Thrown when no active publication policy exists for a document type, or when
 * the policy carried by a generation request does not match the document type.
 */
final class PublicationPolicyNotFoundException extends AcademicCommunicationException
{
    public static function forType(DocumentType $type, ?string $schoolId = null): self
    {
        return new self(
            sprintf(
                'No active publication policy found for document type [%s]%s.',
                $type->value,
                $schoolId !== null ? " in school [{$schoolId}]" : '',
            ),
        );
    }

    public static function mismatchedType(DocumentType $type, int|string $policyId): self
    {
        return new self(
            sprintf(
                'Publication policy [#%s] does not match document type [%s].',
                $policyId,
                $type->value,
            ),
        );
    }
}
