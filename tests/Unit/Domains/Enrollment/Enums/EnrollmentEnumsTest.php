<?php

declare(strict_types=1);

use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Enums\EnrollmentType;
use App\Domains\Enrollment\Domain\Enums\GraduationDecision;
use App\Domains\Enrollment\Domain\Enums\PromotionDecision;
use App\Domains\Enrollment\Domain\Enums\TransferType;

test('enrollment status cases map to the lifecycle', function (): void {
    expect(EnrollmentStatus::cases())
        ->each->toBeInstanceOf(EnrollmentStatus::class);
});

test('enrollment status has a label and color', function (): void {
    expect(EnrollmentStatus::Active->label())->toBe('Actif')
        ->and(EnrollmentStatus::Active->color())->toBe('success')
        ->and(EnrollmentStatus::Suspended->label())->toBe('Suspendu')
        ->and(EnrollmentStatus::Graduated->label())->toBe('Diplômé')
        ->and(EnrollmentStatus::Withdrawn->label())->toBe('Retiré')
        ->and(EnrollmentStatus::Archived->label())->toBe('Archivé')
        ->and(EnrollmentStatus::Archived->color())->toBe('gray');
});

test('only archived status is immutable', function (): void {
    expect(EnrollmentStatus::Archived->isImmutable())->toBeTrue()
        ->and(EnrollmentStatus::Active->isImmutable())->toBeFalse()
        ->and(EnrollmentStatus::Graduated->isImmutable())->toBeFalse();
});

test('status helpers report their state', function (): void {
    expect(EnrollmentStatus::Active->isActive())->toBeTrue()
        ->and(EnrollmentStatus::Suspended->isSuspended())->toBeTrue()
        ->and(EnrollmentStatus::Graduated->isGraduated())->toBeTrue()
        ->and(EnrollmentStatus::Withdrawn->isWithdrawn())->toBeTrue()
        ->and(EnrollmentStatus::Archived->isArchived())->toBeTrue();
});

test('enrollment type labels describe the creation reason', function (): void {
    expect(EnrollmentType::New->label())->toBe('Nouvelle inscription')
        ->and(EnrollmentType::ReEnrollment->label())->toBe('Réinscription')
        ->and(EnrollmentType::Promotion->label())->toBe('Passage en classe supérieure')
        ->and(EnrollmentType::Repetition->label())->toBe('Redoublement')
        ->and(EnrollmentType::TransferIn->label())->toBe('Transfert entrant');
});

test('promotion decisions label the end-of-year outcome', function (): void {
    expect(PromotionDecision::Promoted->label())->toBe('Admis')
        ->and(PromotionDecision::Repeated->label())->toBe('Redouble')
        ->and(PromotionDecision::Skipped->label())->toBe('Sauté une classe');
});

test('transfer types label the transfer kind', function (): void {
    expect(TransferType::Internal->label())->toBe('Transfert interne')
        ->and(TransferType::SchoolTransfer->label())->toBe("Transfert d'école");
});

test('graduation decisions always close the enrollment', function (): void {
    expect(GraduationDecision::Graduated->closesEnrollment())->toBeTrue()
        ->and(GraduationDecision::Conditional->closesEnrollment())->toBeTrue();
});
