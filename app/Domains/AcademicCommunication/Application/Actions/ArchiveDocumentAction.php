<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Actions;

use App\Domains\AcademicCommunication\Domain\Contracts\RecordsLifecycleContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Events\DocumentArchived;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Archives a published document (SPR-012). Archiving is terminal: an archived
 * document leaves the active registers while its immutable published versions
 * (and their audit trail) stay fully readable.
 */
final class ArchiveDocumentAction
{
    public function __construct(
        private readonly RecordsLifecycleContract $lifecycle,
    ) {
    }

    public function execute(AcademicDocument $document, ?User $actor = null): AcademicDocument
    {
        return DB::transaction(function () use ($document, $actor): AcademicDocument {
            $document->assertCanTransitionTo(DocumentStatus::Archived);

            $document->update([
                'status' => DocumentStatus::Archived,
                'archived_at' => now(),
            ]);

            $document->refresh();

            $this->lifecycle->archive(
                $document->school_id,
                (string) $document->getAttributes()['document_type'],
                $document->getKey(),
                $actor,
            );

            DocumentArchived::dispatch($document, $actor);

            return $document;
        });
    }
}
