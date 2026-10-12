<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Enums;

/**
 * Status of a single completion entry (one student of a request) (SPR-015).
 *
 * Generating composes and persists the immutable snapshot and delegates the
 * official document: the entry becomes Issued. An entry stays Pending when no
 * document could be produced.
 */
enum CompletionRequestEntryStatus: string
{
    case Pending = 'pending';
    case Issued = 'issued';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Issued => 'Émis',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Issued => 'success',
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
