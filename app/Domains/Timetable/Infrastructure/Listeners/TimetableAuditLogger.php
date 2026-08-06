<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Listeners;

use App\Domains\Timetable\Domain\Events\AvailabilityConstraintCreated;
use App\Domains\Timetable\Domain\Events\ConflictDetected;
use App\Domains\Timetable\Domain\Events\ConflictResolved;
use App\Domains\Timetable\Domain\Events\RoomAssigned;
use App\Domains\Timetable\Domain\Events\ScheduleEntryCreated;
use App\Domains\Timetable\Domain\Events\ScheduleEntryUpdated;
use App\Domains\Timetable\Domain\Events\TeacherAssigned;
use App\Domains\Timetable\Domain\Events\TimetableArchived;
use App\Domains\Timetable\Domain\Events\TimetableCreated;
use App\Domains\Timetable\Domain\Events\TimetableDuplicated;
use App\Domains\Timetable\Domain\Events\TimetablePublished;
use App\Domains\Timetable\Infrastructure\Models\AvailabilityConstraint;
use App\Domains\Timetable\Infrastructure\Models\ScheduleEntry;
use App\Domains\Timetable\Infrastructure\Models\Timetable;
use App\Domains\Timetable\Infrastructure\Models\TimetableConflict;
use App\Domains\Timetable\Infrastructure\Models\TimetableVersion;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Writes the security audit trail (AuditLog) for every Timetable event and, on
 * publication, the append-only TimetableVersion snapshot. Snapshot rows are
 * never updated or removed.
 */
final class TimetableAuditLogger
{
    public function handleTimetableCreated(TimetableCreated $event): void
    {
        $this->logTimetable($event->timetable, 'timetable_created', 'Timetable created', $event->actor);
    }

    public function handleTimetablePublished(TimetablePublished $event): void
    {
        $this->logTimetable($event->timetable, 'timetable_published', "Timetable published (version {$event->version})", $event->actor);

        TimetableVersion::create([
            'school_id' => $event->timetable->school_id,
            'timetable_id' => $event->timetable->getKey(),
            'version' => $event->version,
            'status' => $event->timetable->status->value,
            'snapshot' => $this->snapshotEntries($event->timetable),
            'reason' => 'Published',
            'created_by' => $event->actor?->getKey(),
        ]);
    }

    public function handleTimetableArchived(TimetableArchived $event): void
    {
        $this->logTimetable($event->timetable, 'timetable_archived', 'Timetable archived', $event->actor);
    }

    public function handleTimetableDuplicated(TimetableDuplicated $event): void
    {
        $this->logTimetable($event->timetable, 'timetable_duplicated', 'Timetable duplicated', $event->actor);
    }

    public function handleScheduleEntryCreated(ScheduleEntryCreated $event): void
    {
        $this->logEntry($event->entry, 'schedule_entry_created', 'Schedule entry created', $event->actor);
    }

    public function handleScheduleEntryUpdated(ScheduleEntryUpdated $event): void
    {
        $description = count($event->changes) > 0
            ? 'Schedule entry updated: '.implode(', ', array_keys($event->changes))
            : 'Schedule entry updated';

        $this->logEntry($event->entry, 'schedule_entry_updated', $description, $event->actor, $event->changes);
    }

    public function handleTeacherAssigned(TeacherAssigned $event): void
    {
        $this->logEntry($event->entry, 'teacher_assigned', "Teacher assigned (staff #{$event->teacherId})", $event->actor, [
            'staff_id' => $event->teacherId,
        ]);
    }

    public function handleRoomAssigned(RoomAssigned $event): void
    {
        $this->logEntry($event->entry, 'room_assigned', "Room assigned ({$event->roomId})", $event->actor, [
            'room_id' => $event->roomId,
        ]);
    }

    public function handleConflictDetected(ConflictDetected $event): void
    {
        foreach ($event->conflicts as $conflict) {
            $this->logConflict($conflict, 'conflict_detected', $conflict->reason ?? 'Conflict detected', $event->actor);
        }
    }

    public function handleConflictResolved(ConflictResolved $event): void
    {
        $this->logConflict($event->conflict, 'conflict_resolved', 'Conflict resolved', $event->actor);
    }

    public function handleAvailabilityConstraintCreated(AvailabilityConstraintCreated $event): void
    {
        $this->logConstraint($event->constraint, 'availability_constraint_created', 'Availability constraint created', $event->actor);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function snapshotEntries(Timetable $timetable): array
    {
        return $timetable->entries()
            ->orderBy('day_of_week')
            ->orderBy('period_number')
            ->get()
            ->map(fn (ScheduleEntry $entry): array => [
                'id' => $entry->getKey(),
                'uuid' => $entry->uuid,
                'academic_subject_id' => $entry->academic_subject_id,
                'school_class_id' => $entry->school_class_id,
                'class_section_id' => $entry->class_section_id,
                'staff_id' => $entry->staff_id,
                'room_id' => $entry->room_id,
                'day_of_week' => $entry->day_of_week,
                'period_number' => $entry->period_number,
                'starts_at' => $entry->starts_at,
                'ends_at' => $entry->ends_at,
                'duration_minutes' => $entry->duration_minutes,
                'entry_type' => $entry->entry_type?->value,
                'status' => $entry->status?->value,
            ])
            ->all();
    }

    private function logTimetable(Timetable $timetable, string $action, string $description, ?User $actor = null): void
    {
        AuditLog::create([
            'school_id' => $timetable->school_id,
            'auditable_type' => $timetable::class,
            'auditable_id' => $timetable->getKey(),
            'action' => $action,
            'event' => class_basename($timetable),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function logEntry(ScheduleEntry $entry, string $action, string $description, ?User $actor = null, ?array $payload = null): void
    {
        AuditLog::create([
            'school_id' => $entry->school_id,
            'auditable_type' => $entry::class,
            'auditable_id' => $entry->getKey(),
            'action' => $action,
            'event' => class_basename($entry),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function logConflict(TimetableConflict $conflict, string $action, string $description, ?User $actor = null, ?array $payload = null): void
    {
        AuditLog::create([
            'school_id' => $conflict->school_id,
            'auditable_type' => $conflict::class,
            'auditable_id' => $conflict->getKey(),
            'action' => $action,
            'event' => class_basename($conflict),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);
    }

    private function logConstraint(AvailabilityConstraint $constraint, string $action, string $description, ?User $actor = null): void
    {
        AuditLog::create([
            'school_id' => $constraint->school_id,
            'auditable_type' => $constraint::class,
            'auditable_id' => $constraint->getKey(),
            'action' => $action,
            'event' => class_basename($constraint),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);
    }
}
