<?php

declare(strict_types=1);

use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestStatus;

test('the completion lifecycle is forward only', function (): void {
    expect(CompletionRequestStatus::Generated->allowsTransitionFrom(CompletionRequestStatus::Draft))->toBeTrue()
        ->and(CompletionRequestStatus::Published->allowsTransitionFrom(CompletionRequestStatus::Generated))->toBeTrue()
        ->and(CompletionRequestStatus::Archived->allowsTransitionFrom(CompletionRequestStatus::Published))->toBeTrue();
});

test('stages cannot be skipped or replayed', function (): void {
    expect(CompletionRequestStatus::Published->allowsTransitionFrom(CompletionRequestStatus::Draft))->toBeFalse()
        ->and(CompletionRequestStatus::Archived->allowsTransitionFrom(CompletionRequestStatus::Draft))->toBeFalse()
        ->and(CompletionRequestStatus::Archived->allowsTransitionFrom(CompletionRequestStatus::Generated))->toBeFalse()
        ->and(CompletionRequestStatus::Generated->allowsTransitionFrom(CompletionRequestStatus::Published))->toBeFalse()
        ->and(CompletionRequestStatus::Draft->allowsTransitionFrom(CompletionRequestStatus::Published))->toBeFalse();
});

test('published and archived requests are immutable', function (): void {
    expect(CompletionRequestStatus::Draft->isImmutable())->toBeFalse()
        ->and(CompletionRequestStatus::Generated->isImmutable())->toBeFalse()
        ->and(CompletionRequestStatus::Published->isImmutable())->toBeTrue()
        ->and(CompletionRequestStatus::Archived->isImmutable())->toBeTrue();
});
