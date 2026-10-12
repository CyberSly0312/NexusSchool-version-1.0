<?php

declare(strict_types=1);

use App\Domains\IdentityAndVerification\Domain\Enums\VerificationEligibilityVerdict;
use App\Domains\IdentityAndVerification\Domain\ValueObjects\PublicVerificationPolicy;

function publicVerificationPolicy(): PublicVerificationPolicy
{
    return new PublicVerificationPolicy();
}

test('the policy exposes the uniform non-revealing surface string', function (): void {
    $policy = publicVerificationPolicy();

    expect($policy->noPublicRecord())->toBe('no_public_record');
    expect(PublicVerificationPolicy::SURFACE_STRING)->toBe('no_public_record');
});

test('the policy canonicalizes every verdict into its surface string', function (): void {
    $policy = publicVerificationPolicy();

    expect($policy->filter(['verdict' => VerificationEligibilityVerdict::NotPubliclyVerifiable]))
        ->toBe(['verdict' => 'no_public_record']);

    expect($policy->filter(['verdict' => VerificationEligibilityVerdict::AuthenticPublished]))
        ->toBe(['verdict' => 'authentic_published']);

    expect($policy->filter(['verdict' => VerificationEligibilityVerdict::AuthenticArchived]))
        ->toBe(['verdict' => 'authentic_archived']);

    expect($policy->filter(['verdict' => VerificationEligibilityVerdict::AuthenticOfficiallyRevoked]))
        ->toBe(['verdict' => 'authentic_officially_revoked']);

    expect($policy->filter(['verdict' => VerificationEligibilityVerdict::AuthenticSuperseded]))
        ->toBe(['verdict' => 'authentic_superseded']);
});

test('the policy keeps exactly the LEVEL-2 whitelist and drops everything else', function (): void {
    $policy = publicVerificationPolicy();

    expect($policy->filter([
        'verdict' => 'authentic_published',
        'document_number' => 'NS-2027-SCH001-RC-000000145',
        'document_type' => 'report_card',
        'status' => 'published',
        'issued_at' => '2026-08-01T09:00:00+00:00',
    ]))->toBe([
        'verdict' => 'authentic_published',
        'document_number' => 'NS-2027-SCH001-RC-000000145',
        'document_type' => 'report_card',
        'status' => 'published',
        'issued_at' => '2026-08-01T09:00:00+00:00',
    ]);

    expect($policy->filter([
        'verdict' => 'authentic_published',
        'document_number' => 'NS-2027-SCH001-RC-000000145',
        'school_id' => 'test-tenant',
        'academic_document_id' => 42,
        'identity_uuid' => '01JX0000000000000000000000',
        'token' => 'the-plaintext-token',
        'grades' => ['math' => 18],
        'content' => 'full transcript',
        'student_name' => 'Jean Dupont',
        'email' => 'jean@example.test',
    ]))->toBe([
        'verdict' => 'authentic_published',
        'document_number' => 'NS-2027-SCH001-RC-000000145',
    ]);
});

test('the policy drops null values so partially filled verdicts stay minimal', function (): void {
    $policy = publicVerificationPolicy();

    expect($policy->filter([
        'verdict' => 'authentic_officially_revoked',
        'document_number' => 'NS-2027-SCH001-RC-000000145',
        'document_type' => 'report_card',
        'status' => 'revoked',
        'issued_at' => null,
    ]))->toBe([
        'verdict' => 'authentic_officially_revoked',
        'document_number' => 'NS-2027-SCH001-RC-000000145',
        'document_type' => 'report_card',
        'status' => 'revoked',
    ]);
});

test('the policy returns the empty payload when nothing is allowed', function (): void {
    $policy = publicVerificationPolicy();

    expect($policy->filter(['school_id' => 'test-tenant', 'token' => 'secret']))->toBe([]);
    expect($policy->filter([]))->toBe([]);
});
