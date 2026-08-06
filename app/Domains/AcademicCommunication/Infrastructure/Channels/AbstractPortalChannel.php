<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Channels;

use App\Domains\AcademicCommunication\Application\DTOs\PublicationDeliveryOutcome;
use App\Domains\AcademicCommunication\Application\DTOs\PublicationPayload;
use App\Domains\AcademicCommunication\Domain\Contracts\PublicationChannelContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;

/**
 * Base of every portal channel of the Academic Communication Engine (SPR-012).
 * Concrete portals only declare which DocumentChannel they serve; the delivery
 * (recipient grant + scoped HTML) is identical for all portals.
 */
abstract class AbstractPortalChannel implements PublicationChannelContract
{
    use CreatesDocumentRecipients;

    abstract public function channel(): DocumentChannel;

    public function isAvailable(): bool
    {
        return true;
    }

    public function deliver(PublicationPayload $payload): PublicationDeliveryOutcome
    {
        $this->upsertRecipient($payload);

        return PublicationDeliveryOutcome::success([
            'html' => $payload->html,
            'recipient_id' => $payload->recipientId,
        ]);
    }
}
