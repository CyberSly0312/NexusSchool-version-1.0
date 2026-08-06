<?php

declare(strict_types=1);

use App\Domains\Timetable\Application\Services\TimetableService;
use App\Domains\Timetable\Domain\Enums\AvailabilityConstraintType;
use App\Domains\Timetable\Domain\Enums\TimetableStatus;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->academic = makeTimetableAcademicContext($this->schoolId);
});

test('the facade orchestrates create, schedule, publish and archive', function (): void {
    $service = app(TimetableService::class);

    $timetable = $service->createTimetable(
        \App\Domains\Timetable\Application\DTOs\CreateTimetableData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'EDT Principal',
            'academic_calendar_year_id' => $this->academic['year']->id,
            'academic_calendar_term_id' => $this->academic['term']->id,
        ]),
    );

    $entry = $service->createEntry(\App\Domains\Timetable\Application\DTOs\ScheduleEntryData::fromArray([
        'school_id' => $this->schoolId,
        'timetable_id' => $timetable->id,
        'academic_subject_id' => $this->academic['subject']->id,
        'school_class_id' => $this->academic['class']->id,
    ]));

    $published = $service->publish($timetable->id);

    $archived = $service->archive($timetable->id);

    expect($entry->timetable_id)->toBe($timetable->id)
        ->and($published->status)->toBe(TimetableStatus::Published)
        ->and($archived->status)->toBe(TimetableStatus::Archived);
});

test('the facade adds availability constraints and resolves conflicts', function (): void {
    $service = app(TimetableService::class);
    $timetable = makeTimetable($this->schoolId, $this->academic['year']->id, $this->academic['term']->id);
    $teacher = makeTeacher($this->schoolId, [(int) $this->academic['subject']->id]);

    $constraint = $service->addConstraint(\App\Domains\Timetable\Application\DTOs\AvailabilityConstraintData::fromArray([
        'school_id' => $this->schoolId,
        'timetable_id' => $timetable->id,
        'constraint_type' => AvailabilityConstraintType::Teacher->value,
        'target_id' => (string) $teacher->id,
        'day_of_week' => 0,
        'starts_at' => '08:00',
        'ends_at' => '12:00',
    ]));

    $conflict = \App\Domains\Timetable\Infrastructure\Models\TimetableConflict::factory()
        ->forSchool($this->schoolId)
        ->create(['timetable_id' => $timetable->id]);

    $resolved = $service->resolveConflict($conflict->id, 'moved', 'Déplacé en P3');

    expect($constraint->constraint_type)->toBe(AvailabilityConstraintType::Teacher)
        ->and($resolved->isResolved())->toBeTrue()
        ->and($resolved->resolution)->toBe('moved');
});

test('the query service exposes school-scoped aggregates', function (): void {
    makeTeachingGrid($this->schoolId);
    $service = app(TimetableService::class);
    $timetable = makeTimetable($this->schoolId, $this->academic['year']->id, $this->academic['term']->id);
    scheduleLesson($this->schoolId, $timetable->id, $this->academic['subject']->id, $this->academic['class']->id);

    $weekly = $service->query()->weeklyGrid($timetable->id);

    expect($weekly['timetable_id'])->toBe($timetable->id)
        ->and($weekly['days'])->toHaveCount(2)
        ->and($service->query()->unresolvedConflicts($timetable->id))->toHaveCount(0);
});
