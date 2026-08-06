<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Channels;

use App\Domains\AcademicCommunication\Application\DTOs\PublicationPayload;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentRecipient;

/**
 * Shared behaviour of the portal channel drivers (SPR-012). A portal delivery
 * grants the recipient access to the document: a DocumentRecipient row is
 * created (or refreshed) with the recipient's visibility scope, and the
 * pre-rendered scope-redacted HTML is returned for the portal to serve.
 */
trait CreatesDocumentRecipients
{
    private function upsertRecipient(PublicationPayload $payload): void
    {
        DocumentRecipient::updateOrCreate(
            [
                'school_id' => $payload->schoolId,
                'academic_document_id' => $payload->documentId,
                'document_version_id' => $payload->versionId,
                'recipient_type' => $payload->recipientType,
                'recipient_id' => $payload->recipientId,
            ],
            [
                'recipient_label' => $payload->recipientLabel,
                'visibility_scope' => $payload->visibilityScope,
                'delivered_at' => now(),
            ],
        );
    }
}
