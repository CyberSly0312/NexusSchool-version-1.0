<?php

declare(strict_types=1);

use App\Domains\IdentityAndVerification\Application\Services\VerificationEligibility;
use App\Domains\IdentityAndVerification\Domain\Enums\VerificationEligibilityVerdict;

test('unpublished document states are never publicly verifiable', function (): void {
    $eligibility = new VerificationEligibility();

    foreach (['draft', 'generated', 'reviewed', 'approved'] as $status) {
        expect($eligibility->verdictForDocumentStatus($status))
            ->toBe(VerificationEligibilityVerdict::NotPubliclyVerifiable);
        expect($eligibility->isPubliclyVerifiable($status))->toBeFalse();
    }
});

test('a published document is the only currently-valid verdict', function (): void {
    $eligibility = new VerificationEligibility();

    expect($eligibility->verdictForDocumentStatus('published'))
        ->toBe(VerificationEligibilityVerdict::AuthenticPublished);
    expect($eligibility->isPubliclyVerifiable('published'))->toBeTrue();
});

test('an archived document remains historically verifiable and distinct from published', function (): void {
    $eligibility = new VerificationEligibility();

    expect($eligibility->verdictForDocumentStatus('archived'))
        ->toBe(VerificationEligibilityVerdict::AuthenticArchived);
    expect($eligibility->isPubliclyVerifiable('archived'))->toBeTrue();
    expect(VerificationEligibilityVerdict::AuthenticArchived)
        ->not->toBe(VerificationEligibilityVerdict::AuthenticPublished);
});
