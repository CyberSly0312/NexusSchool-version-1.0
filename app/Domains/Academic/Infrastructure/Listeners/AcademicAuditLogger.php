<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Listeners;

use App\Domains\Academic\Domain\Events\CalendarTermCreated;
use App\Domains\Academic\Domain\Events\CalendarYearActivated;
use App\Domains\Academic\Domain\Events\CalendarYearArchived;
use App\Domains\Academic\Domain\Events\CalendarYearCreated;
use App\Domains\Academic\Domain\Events\ClassSectionCreated;
use App\Domains\Academic\Domain\Events\SchoolClassCreated;
use App\Domains\Academic\Domain\Events\StreamCreated;
use App\Domains\Academic\Domain\Events\SubjectCreated;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final class AcademicAuditLogger
{
    public function handleCalendarYearCreated(CalendarYearCreated $event): void
    {
        $this->log($event->calendarYear, 'created', $event->actor);
    }

    public function handleCalendarYearActivated(CalendarYearActivated $event): void
    {
        $this->log($event->calendarYear, 'activated', $event->actor);
    }

    public function handleCalendarYearArchived(CalendarYearArchived $event): void
    {
        $this->log($event->calendarYear, 'archived', $event->actor);
    }

    public function handleCalendarTermCreated(CalendarTermCreated $event): void
    {
        $this->log($event->term, 'created', $event->actor);
    }

    public function handleStreamCreated(StreamCreated $event): void
    {
        $this->log($event->stream, 'created', $event->actor);
    }

    public function handleSchoolClassCreated(SchoolClassCreated $event): void
    {
        $this->log($event->schoolClass, 'created', $event->actor);
    }

    public function handleClassSectionCreated(ClassSectionCreated $event): void
    {
        $this->log($event->section, 'created', $event->actor);
    }

    public function handleSubjectCreated(SubjectCreated $event): void
    {
        $this->log($event->subject, 'created', $event->actor);
    }

    private function log(Model $model, string $action, ?User $actor = null): void
    {
        AuditLog::create([
            'school_id' => $model->school_id,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'action' => $action,
            'event' => class_basename($model),
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
        ]);
    }
}
