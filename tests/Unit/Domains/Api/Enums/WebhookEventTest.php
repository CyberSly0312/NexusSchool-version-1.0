<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Api\Enums;

use App\Domains\Api\Enums\WebhookEvent;
use Tests\TestCase;

class WebhookEventTest extends TestCase
{
    public function test_student_created_label(): void
    {
        $this->assertEquals('Student Created', WebhookEvent::StudentCreated->label());
    }

    public function test_student_updated_label(): void
    {
        $this->assertEquals('Student Updated', WebhookEvent::StudentUpdated->label());
    }

    public function test_enrollment_created_label(): void
    {
        $this->assertEquals('Enrollment Created', WebhookEvent::EnrollmentCreated->label());
    }

    public function test_attendance_recorded_label(): void
    {
        $this->assertEquals('Attendance Recorded', WebhookEvent::AttendanceRecorded->label());
    }

    public function test_grade_published_label(): void
    {
        $this->assertEquals('Grade Published', WebhookEvent::GradePublished->label());
    }

    public function test_payment_received_label(): void
    {
        $this->assertEquals('Payment Received', WebhookEvent::PaymentReceived->label());
    }

    public function test_notification_sent_label(): void
    {
        $this->assertEquals('Notification Sent', WebhookEvent::NotificationSent->label());
    }

    public function test_report_generated_label(): void
    {
        $this->assertEquals('Report Generated', WebhookEvent::ReportGenerated->label());
    }

    public function test_labels(): void
    {
        $labels = WebhookEvent::labels();

        $this->assertCount(8, $labels);
        $this->assertArrayHasKey('student_created', $labels);
        $this->assertArrayHasKey('student_updated', $labels);
        $this->assertArrayHasKey('enrollment_created', $labels);
        $this->assertArrayHasKey('attendance_recorded', $labels);
        $this->assertArrayHasKey('grade_published', $labels);
        $this->assertArrayHasKey('payment_received', $labels);
        $this->assertArrayHasKey('notification_sent', $labels);
        $this->assertArrayHasKey('report_generated', $labels);
    }
}
