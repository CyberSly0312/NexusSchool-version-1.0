<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Policies;

use App\Domains\AcademicCommunication\Domain\Traits\HandlesDocumentAuthorization;

/**
 * Authorization policy for publication policies (SPR-012). Managing policies is
 * a `document.template.manage` / `document.generate` capability of the engine.
 */
final class PublicationPolicyPolicy
{
    use HandlesDocumentAuthorization;
}
