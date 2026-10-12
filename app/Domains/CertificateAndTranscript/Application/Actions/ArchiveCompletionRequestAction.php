<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Application\Actions;

use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestStatus;
use App\Domains\CertificateAndTranscript\Domain\Events\CompletionRequestArchived;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Archives a published completion request for audit (SPR-015). Archived
 * requests stay immutable and leave the active flow.
 */
final class ArchiveCompletionRequestAction
{
    public function execute(CompletionRequest $completionRequest, ?User $actor = null): void
    {
        DB::transaction(function () use ($completionRequest, $actor): void {
            $completionRequest->assertCanTransitionTo(CompletionRequestStatus::Archived);

            $completionRequest->status = CompletionRequestStatus::Archived;
            $completionRequest->archived_at = now();
            $completionRequest->save();

            CompletionRequestArchived::dispatch(
                $completionRequest,
                $completionRequest->completion_document_kind,
                $actor,
            );
        });
    }
}
