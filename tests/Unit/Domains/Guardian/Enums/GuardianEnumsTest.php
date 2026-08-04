<?php

declare(strict_types=1);

use App\Domains\Guardian\Domain\Enums\GuardianStatus;
use App\Domains\Guardian\Domain\Enums\PreferredContactMethod;
use App\Domains\Guardian\Domain\Enums\RelationshipCategory;
use App\Domains\Guardian\Domain\Enums\RelationshipFlag;

test('guardian status covers the lifecycle', function (): void {
    expect(GuardianStatus::cases())->toHaveCount(2);

    expect(GuardianStatus::Active->value)->toBe('active')
        ->and(GuardianStatus::Active->label())->toBe('Actif')
        ->and(GuardianStatus::Active->isImmutable())->toBeFalse();

    expect(GuardianStatus::Archived->value)->toBe('archived')
        ->and(GuardianStatus::Archived->label())->toBe('Archivé')
        ->and(GuardianStatus::Archived->isImmutable())->toBeTrue();
});

test('preferred contact method covers the supported channels', function (): void {
    expect(PreferredContactMethod::cases())->toHaveCount(4);

    expect(PreferredContactMethod::WhatsApp->label())->toBe('WhatsApp');
});

test('relationship categories cover the taxonomy', function (): void {
    expect(RelationshipCategory::cases())->toHaveCount(5);

    expect(RelationshipCategory::Family->label())->toBe('Family')
        ->and(RelationshipCategory::Emergency->label())->toBe('Emergency');
});

test('relationship flags map to their student_guardians columns', function (): void {
    expect(RelationshipFlag::Primary->column())->toBe('is_primary')
        ->and(RelationshipFlag::EmergencyContact->column())->toBe('is_emergency_contact')
        ->and(RelationshipFlag::AuthorizedPickup->column())->toBe('is_authorized_pickup')
        ->and(RelationshipFlag::FinancialResponsible->column())->toBe('is_financial_responsible')
        ->and(RelationshipFlag::NotificationRecipient->column())->toBe('is_notification_recipient')
        ->and(RelationshipFlag::MedicalContact->column())->toBe('is_medical_contact');
});

test('relationship flag labels are explicit', function (): void {
    expect(RelationshipFlag::Primary->label())->toBe('Primary Guardian')
        ->and(RelationshipFlag::AuthorizedPickup->label())->toBe('Authorized Pickup');
});
