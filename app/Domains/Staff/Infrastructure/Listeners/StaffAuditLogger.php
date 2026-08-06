<?php

declare(strict_types=1);

namespace App\Domains\Staff\Infrastructure\Listeners;

use App\Domains\Staff\Domain\Events\DepartmentAssigned;
use App\Domains\Staff\Domain\Events\EmploymentStarted;
use App\Domains\Staff\Domain\Events\EmploymentTerminated;
use App\Domains\Staff\Domain\Events\EmploymentTransferred;
use App\Domains\Staff\Domain\Events\PositionAssigned;
use App\Domains\Staff\Domain\Events\StaffCreated;
use App\Domains\Staff\Domain\Events\StaffRestored;
use App\Domains\Staff\Domain\Events\StaffSuspended;
use App\Domains\Staff\Domain\Events\StaffUpdated;
use App\Domains\Staff\Domain\Events\TeacherSubjectsAssigned;
use App\Domains\Staff\Infrastructure\Models\Assignment;
use App\Domains\Staff\Infrastructure\Models\Employment;
use App\Domains\Staff\Infrastructure\Models\EmploymentHistory;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Infrastructure\Models\TeacherProfile;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Writes the security audit trail (AuditLog) and, for employment lifecycle
 * events, the immutable EmploymentHistory timeline (SPR-006). History rows are
 * append-only; they are never updated or removed.
 */
final class StaffAuditLogger
{
    public function handleStaffCreated(StaffCreated $event): void
    {
        $this->logStaff($event->staff, 'created', 'Staff record created', $event->actor);
    }

    public function handleStaffUpdated(StaffUpdated $event): void
    {
        $this->logStaff($event->staff, 'updated', 'Staff record updated', $event->actor);
    }

    public function handleStaffSuspended(StaffSuspended $event): void
    {
        $description = $event->reason !== null
            ? "Staff suspended: {$event->reason}"
            : 'Staff suspended';

        $this->logStaff($event->staff, 'suspended', $description, $event->actor);
    }

    public function handleStaffRestored(StaffRestored $event): void
    {
        $this->logStaff($event->staff, 'restored', 'Staff reinstated', $event->actor);
    }

    public function handleEmploymentStarted(EmploymentStarted $event): void
    {
        $this->logEmployment($event->employment, 'employment_started', 'Employment contract started', $event->actor);
    }

    public function handleEmploymentTerminated(EmploymentTerminated $event): void
    {
        $description = $event->reason !== null
            ? "Employment terminated: {$event->reason}"
            : 'Employment terminated';

        $this->logEmployment($event->employment, 'employment_terminated', $description, $event->actor, [
            'termination_date' => $event->terminationDate,
        ]);
    }

    public function handleEmploymentTransferred(EmploymentTransferred $event): void
    {
        $this->logEmployment($event->employment, 'employment_transferred', 'Employment transferred between campuses', $event->actor, [
            'from_school_id' => $event->fromSchoolId,
            'to_school_id' => $event->toSchoolId,
        ]);
    }

    public function handleDepartmentAssigned(DepartmentAssigned $event): void
    {
        $this->logAssignment($event->assignment, 'department_assigned', 'Staff assigned to department', $event->actor);
    }

    public function handlePositionAssigned(PositionAssigned $event): void
    {
        $this->logAssignment($event->assignment, 'position_assigned', 'Staff assigned to position', $event->actor);
    }

    public function handleTeacherSubjectsAssigned(TeacherSubjectsAssigned $event): void
    {
        $subjects = $event->teacherProfile->teaching_subject_ids ?? [];

        $this->logTeacherProfile($event->teacherProfile, 'teacher_subjects_assigned', 'Teaching subjects assigned', $event->actor, [
            'teaching_subject_ids' => $subjects,
        ]);
    }

    private function logStaff(Staff $staff, string $action, string $description, ?User $actor = null): void
    {
        AuditLog::create([
            'school_id' => $staff->school_id,
            'auditable_type' => $staff::class,
            'auditable_id' => $staff->getKey(),
            'action' => $action,
            'event' => class_basename($staff),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function logEmployment(Employment $employment, string $event, string $description, ?User $actor = null, ?array $payload = null): void
    {
        AuditLog::create([
            'school_id' => $employment->school_id,
            'auditable_type' => $employment::class,
            'auditable_id' => $employment->getKey(),
            'action' => $event,
            'event' => class_basename($employment),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);

        EmploymentHistory::create([
            'school_id' => $employment->school_id,
            'employment_id' => $employment->getKey(),
            'staff_id' => $employment->staff_id,
            'event' => $event,
            'description' => $description,
            'payload' => $payload,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
            'occurred_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function logAssignment(Assignment $assignment, string $event, string $description, ?User $actor = null, ?array $payload = null): void
    {
        AuditLog::create([
            'school_id' => $assignment->school_id,
            'auditable_type' => $assignment::class,
            'auditable_id' => $assignment->getKey(),
            'action' => $event,
            'event' => class_basename($assignment),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);

        if ($assignment->employment_id !== null) {
            EmploymentHistory::create([
                'school_id' => $assignment->school_id,
                'employment_id' => $assignment->employment_id,
                'staff_id' => $assignment->staff_id,
                'event' => $event,
                'description' => $description,
                'payload' => $payload,
                'user_id' => $actor?->getKey(),
                'user_type' => $actor !== null ? $actor::class : null,
                'occurred_at' => now(),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function logTeacherProfile(TeacherProfile $profile, string $event, string $description, ?User $actor = null, ?array $payload = null): void
    {
        AuditLog::create([
            'school_id' => $profile->school_id,
            'auditable_type' => $profile::class,
            'auditable_id' => $profile->getKey(),
            'action' => $event,
            'event' => class_basename($profile),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);
    }
}
