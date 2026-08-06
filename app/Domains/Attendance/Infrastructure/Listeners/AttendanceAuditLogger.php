<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Infrastructure\Listeners;

use App\Domains\Attendance\Domain\Events\AttendanceAlertGenerated;
use App\Domains\Attendance\Domain\Events\AttendanceCorrected;
use App\Domains\Attendance\Domain\Events\AttendanceLocked;
use App\Domains\Attendance\Domain\Events\AttendanceRecorded;
use App\Domains\Attendance\Domain\Events\AttendanceSessionCreated;
use App\Domains\Attendance\Domain\Events\AttendanceSessionOpened;
use App\Domains\Attendance\Domain\Events\AttendanceUnlocked;
use App\Domains\Attendance\Infrastructure\Models\AttendanceAlert;
use App\Domains\Attendance\Infrastructure\Models\AttendanceRecord;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Writes the security audit trail (AuditLog) for every Attendance event
 * (SPR-008): session creation/opening, recording, late registration,
 * corrections, locking/unlocking and alert generation.
 */
final class AttendanceAuditLogger
{
    public function handleSessionCreated(AttendanceSessionCreated $event): void
    {
        $this->log(
            $event->session,
            'attendance_session_created',
            'Attendance session created',
            $event->actor,
            $this->sessionPayload($event->session),
        );
    }

    public function handleSessionOpened(AttendanceSessionOpened $event): void
    {
        $this->log(
            $event->session,
            'attendance_session_opened',
            'Attendance session opened for recording',
            $event->actor,
            $this->sessionPayload($event->session),
        );
    }

    public function handleRecorded(AttendanceRecorded $event): void
    {
        $this->log(
            $event->record,
            'attendance_recorded',
            "Attendance [{$event->status->value}] recorded for enrollment #{$event->enrollmentId}",
            $event->actor,
            [
                'session_id' => $event->session->getKey(),
                'enrollment_id' => $event->enrollmentId,
                'status' => $event->status->value,
            ],
        );
    }

    public function handleCorrected(AttendanceCorrected $event): void
    {
        $this->log(
            $event->record,
            'attendance_corrected',
            "Attendance corrected [{$event->previousStatus->value} → {$event->newStatus->value}]",
            $event->actor,
            [
                'correction_id' => $event->correctionId,
                'previous_status' => $event->previousStatus->value,
                'new_status' => $event->newStatus->value,
                'reason_type' => $event->reasonType,
            ],
        );
    }

    public function handleLocked(AttendanceLocked $event): void
    {
        $this->log(
            $event->session,
            'attendance_session_locked',
            'Attendance session locked (immutable)',
            $event->actor,
            $this->sessionPayload($event->session),
        );
    }

    public function handleUnlocked(AttendanceUnlocked $event): void
    {
        $this->log(
            $event->session,
            'attendance_session_unlocked',
            'Attendance session unlocked',
            $event->actor,
            $this->sessionPayload($event->session),
        );
    }

    public function handleAlertGenerated(AttendanceAlertGenerated $event): void
    {
        $this->log(
            $event->alert,
            'attendance_alert_generated',
            "Attendance alert [{$event->alert->alert_type}] (severity {$event->alert->severity})",
            $event->actor,
            [
                'alert_type' => $event->alert->alert_type,
                'severity' => $event->alert->severity,
                'observed_value' => $event->alert->observed_value,
            ],
        );
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function log(
        AttendanceSession|AttendanceRecord|AttendanceAlert $auditable,
        string $action,
        string $description,
        ?User $actor = null,
        ?array $payload = null,
    ): void {
        AuditLog::create([
            'school_id' => $auditable->school_id,
            'auditable_type' => $auditable::class,
            'auditable_id' => $auditable->getKey(),
            'action' => $action,
            'event' => class_basename($auditable),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
            'new_values' => $payload,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function sessionPayload(AttendanceSession $session): array
    {
        return [
            'class_id' => $session->school_class_id,
            'section_id' => $session->class_section_id,
            'date' => $session->date?->toDateString(),
            'period_number' => $session->period_number,
            'status' => $session->status->value,
        ];
    }
}
