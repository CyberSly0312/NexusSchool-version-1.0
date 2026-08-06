<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Contracts;

use App\Domains\AcademicCommunication\Application\DTOs\PublicationDeliveryOutcome;
use App\Domains\AcademicCommunication\Application\DTOs\PublicationPayload;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;

/**
 * Publication channel driver of the Academic Communication Engine (SPR-012).
 *
 * Every channel (portal, pdf export today; email, sms, push tomorrow) is
 * abstracted behind this contract. A new channel is added by implementing the
 * contract, defining it in the engine config and enabling it in the school's
 * PublicationChannel — never by redesigning the engine.
 */
interface PublicationChannelContract
{
    public function channel(): DocumentChannel;

    public function isAvailable(): bool;

    public function deliver(PublicationPayload $payload): PublicationDeliveryOutcome;
}
