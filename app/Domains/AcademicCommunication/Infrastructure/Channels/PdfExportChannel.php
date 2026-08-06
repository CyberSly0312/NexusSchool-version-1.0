<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Channels;

use App\Domains\AcademicCommunication\Application\DTOs\PublicationDeliveryOutcome;
use App\Domains\AcademicCommunication\Application\DTOs\PublicationPayload;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Contracts\PublicationChannelContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\ValueObjects\VisibilityPolicy;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Domains\AcademicCommunication\Infrastructure\Models\GeneratedFile;
use Illuminate\Support\Facades\Storage;

/**
 * PDF export channel of the Academic Communication Engine (SPR-012).
 *
 * Produces the PDF file for a delivery. Administrative scopes receive the full
 * official document; self-scoped recipients (parents, students) receive a
 * scope-redacted rendering so they can never download another person's data.
 * Files are stored on the engine's disk and recorded as GeneratedFile rows.
 */
final class PdfExportChannel implements PublicationChannelContract
{
    use CreatesDocumentRecipients;

    public function __construct(
        private readonly DocumentRendererContract $renderer,
    ) {
    }

    public function channel(): DocumentChannel
    {
        return DocumentChannel::PdfExport;
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function deliver(PublicationPayload $payload): PublicationDeliveryOutcome
    {
        $version = AcademicDocumentVersion::query()
            ->forSchool($payload->schoolId)
            ->find($payload->versionId);

        $template = $payload->templateId !== null
            ? AcademicDocumentTemplate::query()->forSchool($payload->schoolId)->find($payload->templateId)
            : null;

        if ($version === null || $template === null) {
            return PublicationDeliveryOutcome::failure('Cannot export PDF: version or template missing.');
        }

        $document = $version->document;

        $visibility = VisibilityPolicy::fromArray($document->visibility_policy ?? []);

        $bytes = $this->renderer->renderPdf(
            $version,
            $template,
            $this->channel(),
            $visibility,
            $payload->visibilityScope,
        );

        $relativePath = sprintf(
            'documents/%s/%s/%s-delivery-%s.pdf',
            $payload->schoolId,
            $document->document_type->value,
            $document->document_number,
            $payload->visibilityScope->value,
        );

        Storage::disk(config('nexus-academic-communication.disk', 'local'))->put($relativePath, $bytes);

        GeneratedFile::create([
            'school_id' => $payload->schoolId,
            'academic_document_id' => $payload->documentId,
            'document_version_id' => $payload->versionId,
            'file_path' => $relativePath,
            'file_name' => basename($relativePath),
            'disk' => config('nexus-academic-communication.disk', 'local'),
            'mime_type' => 'application/pdf',
            'size_bytes' => strlen($bytes),
            'checksum' => hash('sha256', $bytes),
            'is_current' => false,
        ]);

        $this->upsertRecipient($payload);

        return PublicationDeliveryOutcome::success([
            'file_path' => $relativePath,
            'size_bytes' => strlen($bytes),
        ]);
    }
}
