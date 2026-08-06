<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Actions;

use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\Services\DocumentNumberGenerator;
use App\Domains\AcademicCommunication\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\ApprovalStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Events\DocumentGenerated;
use App\Domains\AcademicCommunication\Domain\Events\DocumentVersionCreated;
use App\Domains\AcademicCommunication\Domain\Exceptions\PublicationPolicyNotFoundException;
use App\Domains\AcademicCommunication\Domain\Exceptions\TemplateNotFoundException;
use App\Domains\AcademicCommunication\Domain\ValueObjects\DocumentNumber;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Domains\AcademicCommunication\Infrastructure\Models\GeneratedFile;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Core generation action of the Academic Communication Engine (SPR-012).
 *
 * Resolves the template and publication policy, issues the permanent document
 * identity, persists the aggregate root and its first version, renders the
 * official PDF file, and transitions the document from Draft to Generated. If
 * the policy requires approval the document is flagged pending; publication
 * happens through PublishDocumentAction, never here.
 *
 * Content is composed by the typed generation actions (report card, minutes,
 * …) and passed through the DTO; this action only knows how to persist and
 * render an AcademicDocument.
 */
final class GenerateDocumentAction
{
    public function __construct(
        private readonly DocumentNumberGenerator $numberGenerator,
        private readonly DocumentRendererContract $renderer,
        private readonly AcademicFrameworkContract $academicFramework,
    ) {
    }

    /**
     * @param  array<string, mixed>|null  $content
     */
    public function execute(
        GenerateDocumentData $data,
        ?User $actor = null,
        ?DocumentOrigin $origin = null,
        ?array $content = null,
    ): AcademicDocument {
        return DB::transaction(function () use ($data, $actor, $origin, $content): AcademicDocument {
            $template = $this->resolveTemplate($data);
            $policy = $this->resolvePolicy($data);
            $number = $this->numberGenerator->next(
                $data->schoolId,
                $data->documentType,
                $this->yearLabel($data),
            );

            $document = AcademicDocument::create([
                'school_id' => $data->schoolId,
                'document_number' => $number->toString(),
                'document_type' => $data->documentType,
                'title' => $data->title,
                'status' => DocumentStatus::Generated,
                'approval_status' => $policy->requires_approval ? ApprovalStatus::Pending : ApprovalStatus::NotRequired,
                'approval_required' => $policy->requires_approval,
                'origin' => $origin ?? $data->origin,
                'academic_calendar_year_id' => $data->yearId,
                'academic_calendar_term_id' => $data->termId,
                'school_class_id' => $data->classId,
                'class_section_id' => $data->sectionId,
                'student_id' => $data->studentId,
                'staff_id' => $data->staffId,
                'council_session_id' => $data->councilSessionId,
                'academic_document_template_id' => $template->getKey(),
                'publication_policy_id' => $policy->getKey(),
                'content' => $content ?? $data->context,
                'content_version' => 1,
                'visibility_policy' => $policy->visibilityPolicy()->toArray(),
                'generated_by' => $actor?->getKey(),
                'generated_at' => now(),
                'metadata' => [
                    'template' => $template->code,
                    'policy' => $policy->code,
                    'year_label' => $number->academicYear,
                ],
            ]);

            $version = AcademicDocumentVersion::create([
                'school_id' => $data->schoolId,
                'academic_document_id' => $document->getKey(),
                'version_number' => 1,
                'status' => DocumentStatus::Generated,
                'content' => $content ?? $data->context,
                'content_hash' => AcademicDocumentVersion::hashContent($content ?? $data->context),
                'is_published' => false,
                'created_by' => $actor?->getKey(),
            ]);

            $this->renderOfficialFile($document, $version, $template);

            $document->refresh();

            DocumentVersionCreated::dispatch($document, $version, false, $actor);
            DocumentGenerated::dispatch($document, $actor);

            return $document;
        });
    }

    private function resolveTemplate(GenerateDocumentData $data): AcademicDocumentTemplate
    {
        if ($data->templateId !== null) {
            $template = AcademicDocumentTemplate::query()
                ->forSchool($data->schoolId)
                ->whereKey($data->templateId)
                ->first();
        } else {
            $template = AcademicDocumentTemplate::query()
                ->forSchool($data->schoolId)
                ->where('document_type', $data->documentType)
                ->where('is_active', true)
                ->orderByDesc('is_default')
                ->orderByDesc('id')
                ->first();
        }

        if ($template === null || $template->document_type !== $data->documentType) {
            throw TemplateNotFoundException::forType($data->documentType, $data->schoolId);
        }

        return $template;
    }

    private function resolvePolicy(GenerateDocumentData $data): PublicationPolicy
    {
        if ($data->policyId !== null) {
            $policy = PublicationPolicy::query()
                ->forSchool($data->schoolId)
                ->whereKey($data->policyId)
                ->first();
        } else {
            $policy = PublicationPolicy::query()
                ->forSchool($data->schoolId)
                ->where('document_type', $data->documentType)
                ->where('is_active', true)
                ->orderByDesc('is_default')
                ->orderByDesc('id')
                ->first();
        }

        if ($policy === null || $policy->document_type !== $data->documentType) {
            if ($policy === null) {
                throw PublicationPolicyNotFoundException::forType($data->documentType, $data->schoolId);
            }

            throw PublicationPolicyNotFoundException::mismatchedType($data->documentType, $policy->getKey());
        }

        return $policy;
    }

    private function yearLabel(GenerateDocumentData $data): ?string
    {
        if ($data->yearId === null) {
            return null;
        }

        return $this->academicFramework->year($data->schoolId, $data->yearId)?->identityYear();
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
            ->where('document_version_id', $version->getKey())
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
