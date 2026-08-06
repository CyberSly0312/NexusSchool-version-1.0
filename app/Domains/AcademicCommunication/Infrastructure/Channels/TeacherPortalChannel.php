<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Channels;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;

/**
 * Teacher portal (SPR-012). Serves documents (class summaries, teaching
 * material) to the teaching staff.
 */
final class TeacherPortalChannel extends AbstractPortalChannel
{
    public function channel(): DocumentChannel
    {
        return DocumentChannel::TeacherPortal;
    }
}
