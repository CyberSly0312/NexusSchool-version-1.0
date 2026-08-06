<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Exceptions;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;

final class InvalidStatusTransitionException extends AcademicCommunicationException
{
    public static function from(DocumentStatus $from, DocumentStatus $to): self
    {
        return new self(
            sprintf(
                'Document transition [%s → %s] is not allowed by the document lifecycle.',
                $from->value,
                $to->value,
            ),
        );
    }
}
