<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Application\Actions;

use App\Domains\CertificateAndTranscript\Application\DTOs\CreateCompletionRequestData;
use App\Domains\CertificateAndTranscript\Domain\Contracts\StudentIdentityContract;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestStatus;
use App\Domains\CertificateAndTranscript\Domain\Events\CompletionRequestCreated;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\CompletionRequestException;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Creates a draft completion request for one student (SPR-015).
 *
 * The student identity is validated through the domain contract. The request
 * is created as a Draft; generation is a separate, audited operation.
 */
final class CreateCompletionRequestAction
{
    public function __construct(
        private readonly StudentIdentityContract $studentIdentity,
    ) {
    }

    public function execute(CreateCompletionRequestData $data, ?User $actor = null): CompletionRequest
    {
        return DB::transaction(function () use ($data, $actor): CompletionRequest {
            if ($this->studentIdentity->student($data->schoolId, $data->studentId) === null) {
                throw new CompletionRequestException(
                    "Student [{$data->studentId}] not found in school [{$data->schoolId}].",
                );
            }

            $completionRequest = CompletionRequest::create([
                'school_id' => $data->schoolId,
                'student_id' => $data->studentId,
                'completion_document_kind' => $data->completionDocumentKind,
                'status' => CompletionRequestStatus::Draft,
                'created_by' => $actor?->getKey(),
            ]);

            CompletionRequestCreated::dispatch($completionRequest, $data->completionDocumentKind, $actor);

            return $completionRequest;
        });
    }
}
