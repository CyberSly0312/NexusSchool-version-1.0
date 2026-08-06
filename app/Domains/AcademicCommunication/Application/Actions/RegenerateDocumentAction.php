<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Actions;

use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Events\DocumentVersionCreated;
use App\Domains\AcademicCommunication\Domain\Exceptions\DocumentImmutableException;
use App\Domains\AcademicCommunication\Domain\Exceptions\TemplateNotFoundException;
use App\Domains\AcademicCommunication\Domain\ValueObjects\DocumentNumber;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Domains\AcademicCommunication\Infrastructure\Models\GeneratedFile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Regenerates a published document with a new version (SPR-012).
 *
 * Published versions are immutable and never overwritten. Regeneration snapshots
 * the corrected content as a new version under the same permanent identity and
 * re-renders the official file. The published version keeps its status and its
 * recipients — the new version becomes the current one and replaces it for any
 * future publication.
 */
final class RegenerateDocumentAction
{
    public function __construct(
        private readonly DocumentRendererContract $renderer,
    ) {
    }

    public function execute(AcademicDocument $document, array $content, ?User $actor = null): AcademicDocumentVersion
    {
        return DB::transaction(function () use ($document, $content, $actor): AcademicDocumentVersion {
            if ($document->isArchived()) {
                throw DocumentImmutableException::forDocument($document->getKey(), $document->status->value);
            }

            $template = $document->template;

            if ($template === null) {
                throw TemplateNotFoundException::forType($document->document_type, $document->school_id);
            }

            $published = $document->currentVersion;
            $nextNumber = (int) ($published?->version_number ?? 0) + 1;

            $version = AcademicDocumentVersion::create([
                'school_id' => $document->school_id,
                'academic_document_id' => $document->getKey(),
                'version_number' => $nextNumber,
                'status' => $document->status,
                'content' => $content,
                'content_hash' => AcademicDocumentVersion::hashContent($content),
                'is_published' => $document->isPublished(),
                'published_at' => $document->isPublished() ? now() : null,
                'created_by' => $actor?->getKey(),
            ]);

            $document->update([
                'content' => $content,
                'content_version' => $nextNumber,
            ]);

            $this->renderOfficialFile($document, $version, $template);

            DocumentVersionCreated::dispatch($document->refresh(), $version, true, $actor);

            return $version;
        });
    }

    private function renderOfficialFile(
        AcademicDocument $document,
        AcademicDocumentVersion $version,
        AcademicDocumentTemplate $template,
    ): void {
        $bytes = $this->renderer->renderPdf($version, $template, DocumentChannel::PdfExport);

        $disk = config('nexus-academic-communication.disk', 'local');
        $number = DocumentNumber::fromString($document->document_number);
        $relativePath = sprintf(
            'documents/%s/%s/%s-v%d.pdf',
            $document->school_id,
            $number->documentType->value,
            $number->toString(),
            $version->version_number,
        );

        Storage::disk($disk)->put($relativePath, $bytes);

        GeneratedFile::query()
            ->forSchool($document->school_id)
            ->where('academic_document_id', $document->getKey())
            ->update(['is_current' => false]);

        GeneratedFile::create([
            'school_id' => $document->school_id,
            'academic_document_id' => $document->getKey(),
            'document_version_id' => $version->getKey(),
            'file_path' => $relativePath,
            'file_name' => basename($relativePath),
            'disk' => $disk,
            'mime_type' => 'application/pdf',
            'size_bytes' => strlen($bytes),
            'checksum' => hash('sha256', $bytes),
            'is_current' => true,
        ]);
    }
}
