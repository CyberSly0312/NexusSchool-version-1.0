<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\References;

/**
 * The official completion document produced by the Academic Communication
 * engine (SPR-015).
 */
final readonly class AcademicDocumentReference
{
    public function __construct(
        public int|string $id,
        public string $documentNumber,
        public string $status,
    ) {
    }
}
