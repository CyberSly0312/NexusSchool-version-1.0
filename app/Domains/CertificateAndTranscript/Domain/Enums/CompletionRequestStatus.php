<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Enums;

/**
 * Lifecycle of a completion request (SPR-015).
 *
 * Draft → Generated → Published → Archived, forward-only and immutable once
 * published, mirroring the Report Card run lifecycle.
 */
enum CompletionRequestStatus: string
{
    case Draft = 'draft';
    case Generated = 'generated';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Generated => 'Généré',
            self::Published => 'Publié',
            self::Archived => 'Archivé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Generated => 'info',
            self::Published => 'success',
            self::Archived => 'gray',
        };
    }

    /**
     * Entries and documents are immutable once the request is published.
     */
    public function isImmutable(): bool
    {
        return $this === self::Published || $this === self::Archived;
    }

    /**
     * True when the engine is allowed to transition from $from to $this.
     */
    public function allowsTransitionFrom(self $from): bool
    {
        return match ($this) {
            self::Generated => $from === self::Draft,
            self::Published => $from === self::Generated,
            self::Archived => $from === self::Published,
            self::Draft => false,
        };
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $status): string => $status->label(),
            self::cases(),
        );
    }
}
