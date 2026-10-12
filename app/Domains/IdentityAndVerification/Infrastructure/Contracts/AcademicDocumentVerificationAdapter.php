<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Infrastructure\Contracts;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\IdentityAndVerification\Domain\Contracts\AcademicDocumentVerificationContract;
use App\Domains\IdentityAndVerification\Domain\References\DocumentVerificationState;

/**
 * AcademicDocumentVerificationContract implementation (SPR-016, ADR-022 C1).
 *
 * The only place SPR-016 reaches the Academic Communication substrate: reads a
 * single document scoped to the requesting school and returns a strictly
 * verification-relevant projection. Any document that does not belong to the
 * given school is indistinguishable from a missing one (uniform null).
 */
final class AcademicDocumentVerificationAdapter implements AcademicDocumentVerificationContract
{
    public function stateForDocument(string $schoolId, int|string $documentId): ?DocumentVerificationState
    {
        $document = AcademicDocument::query()
            ->forSchool($schoolId)
            ->whereKey($documentId)
            ->first();

        if ($document === null) {
            return null;
        }

        $status = $document->getAttribute('status');
        $status = $status instanceof DocumentStatus ? $status->value : ($status === null ? '' : (string) $status);

        $type = $document->getAttribute('document_type');
        $type = $type instanceof DocumentType ? $type->value : ($type === null ? '' : (string) $type);

        $generatedAt = $document->getAttribute('generated_at');

        return new DocumentVerificationState(
            schoolId: (string) $document->school_id,
            documentId: $document->getKey(),
            documentStatus: $status,
            documentNumber: (string) $document->document_number,
            documentType: $type,
            generatedAt: $generatedAt === null ? null : $generatedAt->toIso8601String(),
        );
    }
}
