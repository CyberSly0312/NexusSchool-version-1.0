<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Services;

use App\Domains\AcademicCommunication\Domain\Contracts\PublicationChannelContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Exceptions\PublicationChannelUnavailableException;
use Illuminate\Contracts\Container\Container;

/**
 * Resolves the channel driver for a publication (SPR-012).
 *
 * The driver map lives in `config/nexus-academic-communication.php`, so a new
 * channel is plugged in by implementing the PublicationChannelContract and
 * declaring the driver — never by redesigning the engine. Channels that are
 * declared in the DocumentChannel enum but not implemented yet (Email, SMS,
 * Push) are refused here with the standard unavailable exception.
 */
final class PublicationDispatcher
{
    /**
     * @param  array<string, class-string<PublicationChannelContract>>  $drivers
     */
    public function __construct(
        private readonly array $drivers,
        private readonly Container $container,
    ) {
    }

    public function driver(DocumentChannel $channel): ?PublicationChannelContract
    {
        if (! $channel->isImplemented()) {
            return null;
        }

        $class = $this->drivers[$channel->value] ?? null;

        if ($class === null) {
            return null;
        }

        return $this->container->make($class);
    }

    /**
     * @throws PublicationChannelUnavailableException
     */
    public function requireDriver(DocumentChannel $channel, ?string $schoolId = null): PublicationChannelContract
    {
        if (! $channel->isImplemented()) {
            throw PublicationChannelUnavailableException::notImplemented($channel->value);
        }

        $driver = $this->driver($channel);

        if ($driver === null) {
            throw PublicationChannelUnavailableException::forChannel($channel->value, $schoolId);
        }

        if (! $driver->isAvailable()) {
            throw PublicationChannelUnavailableException::forChannel($channel->value, $schoolId);
        }

        return $driver;
    }
}
