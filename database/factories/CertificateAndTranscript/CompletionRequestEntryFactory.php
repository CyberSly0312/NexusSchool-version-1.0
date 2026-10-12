<?php

declare(strict_types=1);

namespace Database\Factories\CertificateAndTranscript;

use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestEntryStatus;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequestEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompletionRequestEntry>
 */
class CompletionRequestEntryFactory extends Factory
{
    protected $model = CompletionRequestEntry::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'completion_request_id' => CompletionRequest::factory(),
            'content' => [],
            'academic_document_id' => null,
            'academic_document_number' => null,
            'academic_document_type' => null,
            'status' => CompletionRequestEntryStatus::Pending,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function forRequest(int|string $completionRequestId): static
    {
        return $this->state(fn (): array => ['completion_request_id' => $completionRequestId]);
    }

    public function forKind(CompletionDocumentKind $kind): static
    {
        return $this->state(fn (): array => ['academic_document_type' => $kind]);
    }

    public function issued(): static
    {
        return $this->state(fn (): array => ['status' => CompletionRequestEntryStatus::Issued]);
    }
}
