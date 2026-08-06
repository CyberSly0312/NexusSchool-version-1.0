<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Actions;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Marks a generated document as reviewed (SPR-012). Review is an explicit,
 * auditable step of the forward-only lifecycle (Generated → Reviewed → Approved
 * → Published) exercised by a reviewer holding the `document.review` capability.
 */
final class ReviewDocumentAction
{
    public function execute(AcademicDocument $document, ?User $actor = null): AcademicDocument
    {
        return DB::transaction(function () use ($document): AcademicDocument {
            $document->assertCanTransitionTo(DocumentStatus::Reviewed);

            $document->update([
                'status' => DocumentStatus::Reviewed,
                'reviewed_at' => now(),
            ]);

            return $document->refresh();
        });
    }
}
