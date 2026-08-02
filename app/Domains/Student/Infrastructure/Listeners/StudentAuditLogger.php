<?php

declare(strict_types=1);

namespace App\Domains\Student\Infrastructure\Listeners;

use App\Domains\Student\Domain\Events\StudentArchived;
use App\Domains\Student\Domain\Events\StudentCreated;
use App\Domains\Student\Domain\Events\StudentGraduated;
use App\Domains\Student\Domain\Events\StudentReactivated;
use App\Domains\Student\Domain\Events\StudentRestored;
use App\Domains\Student\Domain\Events\StudentSuspended;
use App\Domains\Student\Domain\Events\StudentTransferred;
use App\Domains\Student\Domain\Events\StudentUpdated;
use App\Domains\Student\Domain\Events\StudentWithdrawn;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\AuditLog;
use App\Models\User;

final class StudentAuditLogger
{
    public function handleStudentCreated(StudentCreated $event): void
    {
        $this->log($event->student, 'created', 'Student registered', $event->actor);
    }

    public function handleStudentUpdated(StudentUpdated $event): void
    {
        $this->log($event->student, 'updated', 'Student record updated', $event->actor);
    }

    public function handleStudentTransferred(StudentTransferred $event): void
    {
        $this->log($event->student, 'transferred', 'Student transferred', $event->actor);
    }

    public function handleStudentGraduated(StudentGraduated $event): void
    {
        $this->log($event->student, 'graduated', 'Student graduated', $event->actor);
    }

    public function handleStudentSuspended(StudentSuspended $event): void
    {
        $this->log($event->student, 'suspended', 'Student suspended', $event->actor);
    }

    public function handleStudentReactivated(StudentReactivated $event): void
    {
        $this->log($event->student, 'reactivated', 'Student reactivated', $event->actor);
    }

    public function handleStudentWithdrawn(StudentWithdrawn $event): void
    {
        $this->log($event->student, 'withdrawn', 'Student withdrawn', $event->actor);
    }

    public function handleStudentArchived(StudentArchived $event): void
    {
        $this->log($event->student, 'archived', 'Student archived', $event->actor);
    }

    public function handleStudentRestored(StudentRestored $event): void
    {
        $this->log($event->student, 'restored', 'Student restored from archive', $event->actor);
    }

    private function log(Student $student, string $action, string $description, ?User $actor = null): void
    {
        AuditLog::create([
            'school_id' => $student->school_id,
            'auditable_type' => $student::class,
            'auditable_id' => $student->getKey(),
            'action' => $action,
            'event' => class_basename($student),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);
    }
}
