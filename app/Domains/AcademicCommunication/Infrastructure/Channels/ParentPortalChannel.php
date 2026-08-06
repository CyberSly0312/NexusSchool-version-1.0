<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Channels;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;

/**
 * Parent portal (SPR-012). Serves documents to guardians — always the data of
 * the children they are linked to, never another family's data.
 */
final class ParentPortalChannel extends AbstractPortalChannel
{
    public function channel(): DocumentChannel
    {
        return DocumentChannel::ParentPortal;
    }
}
