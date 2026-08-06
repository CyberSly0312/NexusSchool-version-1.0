<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Policies;

use App\Domains\AcademicCommunication\Domain\Traits\HandlesDocumentAuthorization;

/**
 * Authorization policy for academic document templates (SPR-012). Template
 * management is a dedicated capability (`document.template.manage`).
 */
final class AcademicDocumentTemplatePolicy
{
    use HandlesDocumentAuthorization;
}
