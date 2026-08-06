<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Exceptions;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;

/**
 * Thrown when no active template exists for a document type, or when the
 * active template configuration is invalid.
 */
final class TemplateNotFoundException extends AcademicCommunicationException
{
    public static function forType(DocumentType $type, ?string $schoolId = null): self
    {
        return new self(
            sprintf(
                'No active template found for document type [%s]%s.',
                $type->value,
                $schoolId !== null ? " in school [{$schoolId}]" : '',
            ),
        );
    }
}
