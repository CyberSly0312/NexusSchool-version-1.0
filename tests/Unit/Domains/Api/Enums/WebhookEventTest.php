<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Api\Enums;

use App\Domains\Api\Enums\WebhookEvent;
use Tests\TestCase;

class WebhookEventTest extends TestCase
{
    public function test_student_created_label(): void
    {
        $this->assertEquals('Élève créé', WebhookEvent::StudentCreated->label());
    }

    public function test_student_updated_label(): void
    {
        $this->assertEquals('Élève mis à jour', WebhookEvent::StudentUpdated->label());
    }

    public function test_enrollment_created_label(): void
    {
        $this->assertEquals('Inscription créée', WebhookEvent::EnrollmentCreated->label());
    }

    public function test_attendance_recorded_label(): void
    {
        $this->assertEquals('Présence enregistrée', WebhookEvent::AttendanceRecorded->label());
    }

    public function test_grade_published_label(): void
    {
        $this->assertEquals('Note publiée', WebhookEvent::GradePublished->label());
    }

    public function test_payment_received_label(): void
    {
        $this->assertEquals('Paiement reçu', WebhookEvent::PaymentReceived->label());
    }

    public function test_notification_sent_label(): void
    {
        $this->assertEquals('Notification envoyée', WebhookEvent::NotificationSent->label());
    }

    public function test_report_generated_label(): void
    {
        $this->assertEquals('Rapport généré', WebhookEvent::ReportGenerated->label());
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

        $this->assertEquals('Élève créé', $labels['student_created']);
        $this->assertEquals('Élève mis à jour', $labels['student_updated']);
        $this->assertEquals('Inscription créée', $labels['enrollment_created']);
        $this->assertEquals('Présence enregistrée', $labels['attendance_recorded']);
        $this->assertEquals('Note publiée', $labels['grade_published']);
        $this->assertEquals('Paiement reçu', $labels['payment_received']);
        $this->assertEquals('Notification envoyée', $labels['notification_sent']);
        $this->assertEquals('Rapport généré', $labels['report_generated']);
    }
}
