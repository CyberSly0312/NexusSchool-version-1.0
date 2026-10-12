<?php

declare(strict_types=1);

use App\Domains\IdentityAndVerification\Domain\Enums\VerificationStatus;

test('verification status exposes the ADR-022 controlled vocabulary', function (): void {
    expect(array_column(VerificationStatus::cases(), 'value'))
        ->toBe(['active', 'revoked', 'replaced']);
});

test('verification status provides a label for every case', function (): void {
    foreach (VerificationStatus::cases() as $status) {
        expect($status->label())->toBeString()->not->toBeEmpty();
    }
});

test('verification status options follow the numeric-labels convention', function (): void {
    expect(VerificationStatus::options())
        ->toHaveCount(3)
        ->and(array_values(VerificationStatus::options()))
        ->toBe(['Active', 'Revoked', 'Replaced']);
});

test('only Active may transition to Revoked or Replaced', function (): void {
    expect(VerificationStatus::Revoked->allowsTransitionFrom(VerificationStatus::Active))->toBeTrue()
        ->and(VerificationStatus::Replaced->allowsTransitionFrom(VerificationStatus::Active))->toBeTrue();
});

test('Active is an entry state: nothing may transition to it', function (): void {
    foreach (VerificationStatus::cases() as $from) {
        expect(VerificationStatus::Active->allowsTransitionFrom($from))->toBeFalse();
    }
});

test('the invalidation states are terminal: every exit transition is forbidden', function (): void {
    foreach (VerificationStatus::cases() as $from) {
        foreach (VerificationStatus::cases() as $to) {
            if ($to === VerificationStatus::Revoked || $to === VerificationStatus::Replaced) {
                expect($to->allowsTransitionFrom($from), "{$from->value}->{$to->value}")
                    ->toBe($from === VerificationStatus::Active);
            }
        }
    }
});
