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
            self::StudentCreated => 'Student Created',
            self::StudentUpdated => 'Student Updated',
            self::EnrollmentCreated => 'Enrollment Created',
            self::AttendanceRecorded => 'Attendance Recorded',
            self::GradePublished => 'Grade Published',
            self::PaymentReceived => 'Payment Received',
            self::NotificationSent => 'Notification Sent',
            self::ReportGenerated => 'Report Generated',
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
