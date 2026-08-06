<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Domain\References;

/**
 * The official document produced by the Academic Communication engine for one
 * bulletin (SPR-011).
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
