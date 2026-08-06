<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\ValueObjects;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;

/**
 * Effective configuration of a publication channel for a school (SPR-012).
 * Future channels (Email, SMS, Push) reuse the same contract — the engine only
 * requires a key, an enabled flag and opaque per-channel configuration.
 */
final readonly class DocumentChannelConfig
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        public DocumentChannel $channel,
        public bool $isEnabled,
        public array $config = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'key' => $this->channel->value,
            'enabled' => $this->isEnabled,
            'config' => $this->config,
        ];
    }
}
