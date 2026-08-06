<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Policies;

use App\Domains\AcademicCommunication\Domain\Traits\HandlesDocumentAuthorization;

/**
 * Authorization policy for the AcademicDocument aggregate root (SPR-012). All
 * decisions come from the document.* capabilities, never from roles.
 */
final class AcademicDocumentPolicy
{
    use HandlesDocumentAuthorization;
}
