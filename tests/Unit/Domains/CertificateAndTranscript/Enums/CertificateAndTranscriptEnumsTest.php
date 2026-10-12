<?php

declare(strict_types=1);

use App\Domains\CertificateAndTranscript\Domain\Enums\CertificateEligibilityVerdict;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestEntryStatus;

test('the engine issues exactly two completion documents', function (): void {
    expect(CompletionDocumentKind::Transcript->value)->toBe('transcript')
        ->and(CompletionDocumentKind::Certificate->value)->toBe('certificate');
});

test('certificate eligibility verdicts stay stable', function (): void {
    expect(CertificateEligibilityVerdict::Eligible->value)->toBe('eligible')
        ->and(CertificateEligibilityVerdict::Ineligible->value)->toBe('ineligible');
});

test('entries are pending until a document is issued', function (): void {
    expect(CompletionRequestEntryStatus::Pending->value)->toBe('pending')
        ->and(CompletionRequestEntryStatus::Issued->value)->toBe('issued');
});
