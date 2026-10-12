<?php

declare(strict_types=1);

namespace Database\Factories\CertificateAndTranscript;

use App\Domains\CertificateAndTranscript\Domain\Enums\CertificateEligibilityVerdict;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestStatus;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Domains\Student\Infrastructure\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompletionRequest>
 */
class CompletionRequestFactory extends Factory
{
    protected $model = CompletionRequest::class;

    public function definition(): array
    {
        return [
            'school_id' => 'fac-'.(string) \Illuminate\Support\Str::ulid(),
            'student_id' => Student::factory(),
            'completion_document_kind' => CompletionDocumentKind::Transcript,
            'status' => CompletionRequestStatus::Draft,
            'eligibility_verdict' => null,
            'eligibility_reasons' => null,
            'evidence_citations' => null,
            'created_by' => null,
            'generated_at' => null,
            'reviewed_at' => null,
            'approved_at' => null,
            'published_at' => null,
            'archived_at' => null,
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function forStudent(int|string $studentId): static
    {
        return $this->state(fn (): array => ['student_id' => $studentId]);
    }

    public function transcript(): static
    {
        return $this->state(fn (): array => ['completion_document_kind' => CompletionDocumentKind::Transcript]);
    }

    public function certificate(): static
    {
        return $this->state(fn (): array => ['completion_document_kind' => CompletionDocumentKind::Certificate]);
    }

    public function generated(): static
    {
        return $this->state(fn (): array => [
            'status' => CompletionRequestStatus::Generated,
            'generated_at' => now(),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => CompletionRequestStatus::Published,
            'generated_at' => now()->subHour(),
            'published_at' => now(),
        ]);
    }

    public function eligible(): static
    {
        return $this->state(fn (): array => [
            'eligibility_verdict' => CertificateEligibilityVerdict::Eligible,
            'eligibility_reasons' => ['Eligible per policy thresholds.'],
            'evidence_citations' => [],
        ]);
    }
}
