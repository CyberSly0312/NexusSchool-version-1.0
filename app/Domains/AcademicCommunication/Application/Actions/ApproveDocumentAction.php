<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Actions;

use App\Domains\AcademicCommunication\Application\DTOs\ApproveDocumentData;
use App\Domains\AcademicCommunication\Domain\Enums\ApprovalStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Events\DocumentApproved;
use App\Domains\AcademicCommunication\Domain\Exceptions\DocumentApprovalException;
use App\Domains\AcademicCommunication\Domain\Exceptions\DocumentImmutableException;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentApproval;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Records an approval decision on a document whose policy requires approval
 * (SPR-012).
 *
 * Each role listed in the policy's approval_roles contributes its own
 * DocumentApproval row. When every required role has approved, the document
 * transitions to Approved; a rejection flags the document as Rejected so it
 * can be regenerated. Documents that do not require approval cannot be approved
 * and published directly.
 */
final class ApproveDocumentAction
{
    public function execute(ApproveDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return DB::transaction(function () use ($data, $actor): AcademicDocument {
            $document = AcademicDocument::query()
                ->forSchool($data->schoolId)
                ->findOrFail($data->documentId);

            if (! $document->requiresApproval()) {
                throw DocumentApprovalException::notRequired($document->getKey());
            }

            if ($document->isImmutable() && ! $data->approved) {
                throw DocumentImmutableException::forDocument($document->getKey(), $document->status->value);
            }

            $policy = $document->publicationPolicy;
            $approvalRoles = $policy?->approval_roles ?? [];

            if (! in_array($data->approverRole, $approvalRoles, true)) {
                throw DocumentApprovalException::invalidRole($document->getKey(), $data->approverRole);
            }

            $version = $document->currentVersion;

            $approval = DocumentApproval::create([
                'school_id' => $data->schoolId,
                'academic_document_id' => $document->getKey(),
                'document_version_id' => $version?->getKey(),
                'approver_role' => $data->approverRole,
                'approver_user_id' => $actor?->getKey() ?? $data->actorId,
                'status' => $data->approved ? ApprovalStatus::Approved : ApprovalStatus::Rejected,
                'comment' => $data->comment,
                'decided_at' => now(),
            ]);

            if ($data->approved && $this->allRequiredApproved($document, $approvalRoles)) {
                $document->update([
                    'status' => DocumentStatus::Approved,
                    'approval_status' => ApprovalStatus::Approved,
                ]);
            } elseif (! $data->approved) {
                $document->update([
                    'approval_status' => ApprovalStatus::Rejected,
                ]);
            }

            $document->refresh();

            DocumentApproved::dispatch($document, $approval, $actor);

            return $document;
        });
    }

    /**
     * @param  list<string>  $approvalRoles
     */
    private function allRequiredApproved(AcademicDocument $document, array $approvalRoles): bool
    {
        if ($approvalRoles === []) {
            return true;
        }

        $approved = $document->approvals()
            ->where('status', ApprovalStatus::Approved)
            ->pluck('approver_role')
            ->unique()
            ->all();

        return ! array_diff($approvalRoles, $approved);
    }
}
