<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Application;

use App\Domains\CertificateAndTranscript\Application\Actions\ArchiveCompletionRequestAction;
use App\Domains\CertificateAndTranscript\Application\Actions\CreateCompletionRequestAction;
use App\Domains\CertificateAndTranscript\Application\Actions\GenerateCertificateAction;
use App\Domains\CertificateAndTranscript\Application\Actions\GenerateTranscriptAction;
use App\Domains\CertificateAndTranscript\Application\Actions\PublishCompletionRequestAction;
use App\Domains\CertificateAndTranscript\Application\DTOs\CreateCompletionRequestData;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Models\User;

/**
 * Application facade for the Certificate & Transcript engine (SPR-015).
 * Delegates to the action objects so feature calls stay single-line and actions
 * stay individually testable.
 */
final class CompletionRequestService
{
    public function __construct(
        private readonly CreateCompletionRequestAction $createCompletionRequest,
        private readonly GenerateTranscriptAction $generateTranscript,
        private readonly GenerateCertificateAction $generateCertificate,
        private readonly PublishCompletionRequestAction $publishCompletionRequest,
        private readonly ArchiveCompletionRequestAction $archiveCompletionRequest,
    ) {
    }

    public function createCompletionRequest(CreateCompletionRequestData $data, ?User $actor = null): CompletionRequest
    {
        return $this->createCompletionRequest->execute($data, $actor);
    }

    public function generateTranscript(CompletionRequest $completionRequest, ?User $actor = null): CompletionRequest
    {
        return $this->generateTranscript->execute($completionRequest, $actor);
    }

    public function generateCertificate(CompletionRequest $completionRequest, ?User $actor = null): CompletionRequest
    {
        return $this->generateCertificate->execute($completionRequest, $actor);
    }

    public function publishCompletionRequest(CompletionRequest $completionRequest, ?User $actor = null): CompletionRequest
    {
        return $this->publishCompletionRequest->execute($completionRequest, $actor);
    }

    public function archiveCompletionRequest(CompletionRequest $completionRequest, ?User $actor = null): void
    {
        $this->archiveCompletionRequest->execute($completionRequest, $actor);
    }
}
