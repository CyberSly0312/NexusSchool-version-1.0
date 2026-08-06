<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Channels;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;

/**
 * Student portal (SPR-012). Serves documents to students — always their own
 * data, never another student's data.
 */
final class StudentPortalChannel extends AbstractPortalChannel
{
    public function channel(): DocumentChannel
    {
        return DocumentChannel::StudentPortal;
    }
}
