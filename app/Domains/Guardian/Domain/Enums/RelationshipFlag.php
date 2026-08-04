<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Enums;

/**
 * Role flags a student–guardian relationship may carry. A single guardian can
 * hold several roles for the same student (e.g. primary + emergency contact +
 * authorized pickup). The enum mirrors the `student_guardians` boolean
 * columns through {@see self::column()}.
 */
enum RelationshipFlag: string
{
    case Primary = 'primary';
    case EmergencyContact = 'emergency_contact';
    case AuthorizedPickup = 'authorized_pickup';
    case FinancialResponsible = 'financial_responsible';
    case NotificationRecipient = 'notification_recipient';
    case MedicalContact = 'medical_contact';

    public function label(): string
    {
        return match ($this) {
            self::Primary => 'Primary Guardian',
            self::EmergencyContact => 'Emergency Contact',
            self::AuthorizedPickup => 'Authorized Pickup',
            self::FinancialResponsible => 'Financial Responsible',
            self::NotificationRecipient => 'Notification Recipient',
            self::MedicalContact => 'Medical Contact',
        };
    }

    public function column(): string
    {
        return match ($this) {
            self::Primary => 'is_primary',
            self::EmergencyContact => 'is_emergency_contact',
            self::AuthorizedPickup => 'is_authorized_pickup',
            self::FinancialResponsible => 'is_financial_responsible',
            self::NotificationRecipient => 'is_notification_recipient',
            self::MedicalContact => 'is_medical_contact',
        };
    }
}
