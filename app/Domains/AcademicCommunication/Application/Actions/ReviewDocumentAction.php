<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Actions;

use App\Domains\AcademicCommunication\Application\DTOs\ReviewDocumentData;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Marks a generated document as reviewed (SPR-012). Review is an explicit,
 * auditable step of the forward-only lifecycle (Generated → Reviewed → Approved
 * → Published) exercised by a reviewer holding the `document.review` capability.
 *
 * The document is resolved inside the command's school, so the caller's tenant
 * context — not the document — establishes the expected school. A document that
 * does not belong to that school is never loaded, and no state is changed when
 * the context is missing or does not match.
 */
final class ReviewDocumentAction
{
    public function execute(ReviewDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return DB::transaction(function () use ($data): AcademicDocument {
            $document = AcademicDocument::query()
                ->forSchool($data->schoolId)
                ->findOrFail($data->documentId);

            $document->assertCanTransitionTo(DocumentStatus::Reviewed);

            $document->update([
                'status' => DocumentStatus::Reviewed,
                'reviewed_at' => now(),
            ]);

            return $document->refresh();
        });
    }
}
