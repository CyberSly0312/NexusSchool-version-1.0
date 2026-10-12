<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Enums;

/**
 * Kind of completion document issued by the engine (SPR-015).
 *
 * A request is for a single student and produces exactly one document of this
 * kind. The transcript is assembled from published Report Card snapshots; the
 * end-of-study certificate is gated on published council + graduation + policy
 * evidence.
 */
enum CompletionDocumentKind: string
{
    case Transcript = 'transcript';
    case Certificate = 'certificate';

    public function label(): string
    {
        return match ($this) {
            self::Transcript => 'Relevé de notes',
            self::Certificate => 'Certificat de fin d\'études',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Transcript => 'info',
            self::Certificate => 'success',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $kind): string => $kind->label(),
            self::cases(),
        );
    }
}
