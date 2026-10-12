<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Application\Actions;

use App\Domains\CertificateAndTranscript\Domain\Contracts\AcademicCommunicationContract;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestEntryStatus;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestStatus;
use App\Domains\CertificateAndTranscript\Domain\Events\CompletionRequestPublished;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Publishes the issued completion document of a generated request (SPR-015).
 *
 * The official document is published through the Academic Communication engine
 * (the contract resolves the channels of the document's publication policy).
 * The request then transitions to Published — entries and documents become
 * immutable.
 */
final class PublishCompletionRequestAction
{
    public function __construct(
        private readonly AcademicCommunicationContract $communication,
    ) {
    }

    public function execute(CompletionRequest $completionRequest, ?User $actor = null): CompletionRequest
    {
        return DB::transaction(function () use ($completionRequest, $actor): CompletionRequest {
            $completionRequest->assertCanTransitionTo(CompletionRequestStatus::Published);

            $entry = $completionRequest->entries()
                ->where('status', CompletionRequestEntryStatus::Issued)
                ->whereNotNull('academic_document_id')
                ->first();

            if ($entry !== null) {
                $this->communication->publishCompletionDocument(
                    $completionRequest->school_id,
                    $entry->academic_document_id,
                    $actor,
                );
            }

            $completionRequest->status = CompletionRequestStatus::Published;
            $completionRequest->published_at = now();
            $completionRequest->save();

            CompletionRequestPublished::dispatch(
                $completionRequest,
                $completionRequest->completion_document_kind,
                $actor,
            );

            return $completionRequest->refresh();
        });
    }
}
