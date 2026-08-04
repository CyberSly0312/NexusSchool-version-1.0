<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Infrastructure\Listeners;

use App\Domains\Enrollment\Domain\Events\EnrollmentCreated;
use App\Domains\Enrollment\Domain\Events\EnrollmentRestored;
use App\Domains\Enrollment\Domain\Events\EnrollmentSuspended;
use App\Domains\Enrollment\Domain\Events\EnrollmentUpdated;
use App\Domains\Enrollment\Domain\Events\StudentGraduated;
use App\Domains\Enrollment\Domain\Events\StudentPromoted;
use App\Domains\Enrollment\Domain\Events\StudentRepeated;
use App\Domains\Enrollment\Domain\Events\StudentTransferred;
use App\Domains\Enrollment\Domain\Events\StudentWithdrawn;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentHistory;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Writes the security audit trail (AuditLog) and the immutable business
 * timeline (EnrollmentHistory) for every enrollment lifecycle event.
 */
final class EnrollmentAuditLogger
{
    public function handleEnrollmentCreated(EnrollmentCreated $event): void
    {
        $this->log($event->enrollment, 'created', 'Enrollment registered', $event->actor);
        $this->history($event->enrollment, 'created', 'Enrollment registered', $event->actor);
    }

    public function handleEnrollmentUpdated(EnrollmentUpdated $event): void
    {
        $this->log($event->enrollment, 'updated', 'Enrollment updated', $event->actor);
        $this->history($event->enrollment, 'updated', 'Enrollment updated', $event->actor);
    }

    public function handleStudentPromoted(StudentPromoted $event): void
    {
        $this->log($event->enrollment, 'promoted', 'Student promoted', $event->actor);
        $this->history($event->enrollment, 'promoted', 'Student promoted', $event->actor, [
            'decision' => $event->promotion->decision?->value,
            'to_class_id' => $event->promotion->to_class_id,
        ]);
    }

    public function handleStudentRepeated(StudentRepeated $event): void
    {
        $this->log($event->enrollment, 'repeated', 'Student repeated class', $event->actor);
        $this->history($event->enrollment, 'repeated', 'Student repeated class', $event->actor, [
            'decision' => $event->promotion->decision?->value,
            'to_class_id' => $event->promotion->to_class_id,
        ]);
    }

    public function handleStudentTransferred(StudentTransferred $event): void
    {
        $this->log($event->enrollment, 'transferred', 'Student transferred', $event->actor);
        $this->history($event->enrollment, 'transferred', 'Student transferred', $event->actor, [
            'transfer_type' => $event->transfer->transfer_type?->value,
            'to_class_id' => $event->transfer->to_class_id,
            'to_school_id' => $event->transfer->to_school_id,
        ]);
    }

    public function handleStudentGraduated(StudentGraduated $event): void
    {
        $this->log($event->enrollment, 'graduated', 'Student graduated', $event->actor);
        $this->history($event->enrollment, 'graduated', 'Student graduated', $event->actor, [
            'decision' => $event->graduation->decision?->value,
            'certificate_reference' => $event->graduation->certificate_reference,
        ]);
    }

    public function handleStudentWithdrawn(StudentWithdrawn $event): void
    {
        $this->log($event->enrollment, 'withdrawn', 'Student withdrawn', $event->actor);
        $this->history($event->enrollment, 'withdrawn', 'Student withdrawn', $event->actor, [
            'exit_reason' => $event->enrollment->exit_reason,
        ]);
    }

    public function handleEnrollmentSuspended(EnrollmentSuspended $event): void
    {
        $this->log($event->enrollment, 'suspended', 'Enrollment suspended', $event->actor);
        $this->history($event->enrollment, 'suspended', 'Enrollment suspended', $event->actor, [
            'reason' => $event->enrollment->suspension_reason,
        ]);
    }

    public function handleEnrollmentRestored(EnrollmentRestored $event): void
    {
        $this->log($event->enrollment, 'restored', 'Enrollment restored', $event->actor);
        $this->history($event->enrollment, 'restored', 'Enrollment restored', $event->actor);
    }

    private function log(Enrollment $enrollment, string $action, string $description, ?User $actor = null): void
    {
        AuditLog::create([
            'school_id' => $enrollment->school_id,
            'auditable_type' => $enrollment::class,
            'auditable_id' => $enrollment->getKey(),
            'action' => $action,
            'event' => class_basename($enrollment),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function history(
        Enrollment $enrollment,
        string $event,
        string $description,
        ?User $actor = null,
        ?array $payload = null,
    ): void {
        EnrollmentHistory::create([
            'school_id' => $enrollment->school_id,
            'enrollment_id' => $enrollment->getKey(),
            'student_id' => $enrollment->student_id,
            'event' => $event,
            'description' => $description,
            'payload' => $payload,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
            'occurred_at' => now(),
        ]);
    }
}
