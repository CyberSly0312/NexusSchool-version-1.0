<?php

declare(strict_types=1);

namespace App\Domains\Api\Enums;

enum WebhookEvent: string
{
    case StudentCreated = 'student_created';
    case StudentUpdated = 'student_updated';
    case EnrollmentCreated = 'enrollment_created';
    case AttendanceRecorded = 'attendance_recorded';
    case GradePublished = 'grade_published';
    case PaymentReceived = 'payment_received';
    case NotificationSent = 'notification_sent';
    case ReportGenerated = 'report_generated';

    public function label(): string
    {
        return match ($this) {
            self::StudentCreated => 'Élève créé',
            self::StudentUpdated => 'Élève mis à jour',
            self::EnrollmentCreated => 'Inscription créée',
            self::AttendanceRecorded => 'Présence enregistrée',
            self::GradePublished => 'Note publiée',
            self::PaymentReceived => 'Paiement reçu',
            self::NotificationSent => 'Notification envoyée',
            self::ReportGenerated => 'Rapport généré',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases()),
        );
    }
}
