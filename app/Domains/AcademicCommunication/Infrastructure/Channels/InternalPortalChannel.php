<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Channels;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;

/**
 * Internal portal of the school (SPR-012). Serves documents to the school's
 * administrative staff (Director, Secretary, Teacher scopes).
 */
final class InternalPortalChannel extends AbstractPortalChannel
{
    public function channel(): DocumentChannel
    {
        return DocumentChannel::InternalPortal;
    }
}
