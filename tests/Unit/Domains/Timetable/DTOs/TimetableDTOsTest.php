<?php

declare(strict_types=1);

use App\Domains\Timetable\Application\DTOs\CreateTimetableData;
use App\Domains\Timetable\Application\DTOs\GenerateTimetableData;
use App\Domains\Timetable\Application\DTOs\ScheduleEntryData;
use App\Domains\Timetable\Domain\Enums\ScheduleEntryType;
use App\Domains\Timetable\Domain\Enums\TimetableStatus;

test('create timetable data normalizes foreign key fields', function (): void {
    $data = CreateTimetableData::fromArray([
        'school_id' => 'sch_1',
        'name' => 'Emploi du temps',
        'academic_calendar_year_id' => '42',
        'academic_calendar_term_id' => '',
        'code' => '  TT-2026  ',
    ]);

    expect($data->academicYearId)->toBe(42)
        ->and($data->academicTermId)->toBeNull()
        ->and($data->code)->toBe('TT-2026')
        ->and($data->status)->toBe(TimetableStatus::Draft);
});

test('create timetable data casts string identifiers to ints when numeric', function (): void {
    $data = CreateTimetableData::fromArray([
        'school_id' => 'sch_1',
        'name' => 'EDT 2026-2027',
        'academic_calendar_year_id' => '7',
        'status' => 'draft',
    ]);

    expect($data->toArray())->toMatchArray([
        'school_id' => 'sch_1',
        'academic_calendar_year_id' => 7,
        'status' => 'draft',
        'version' => 1,
    ]);
});

test('schedule entry data infers a duration from the slot boundaries', function (): void {
    $data = ScheduleEntryData::fromArray([
        'school_id' => 'sch_1',
        'timetable_id' => 1,
        'academic_subject_id' => 9,
        'school_class_id' => 3,
        'starts_at' => '08:00',
        'ends_at' => '08:55',
    ]);

    expect($data->inferredDurationMinutes())->toBe(55)
        ->and($data->toArray()['duration_minutes'])->toBe(55)
        ->and($data->entryType)->toBe(ScheduleEntryType::Lesson);
});

test('schedule entry data preserves nullable identity fields', function (): void {
    $data = ScheduleEntryData::fromArray([
        'school_id' => 'sch_1',
        'timetable_id' => 1,
        'academic_subject_id' => 9,
        'school_class_id' => 3,
        'class_section_id' => '',
        'staff_id' => null,
        'room_id' => '  ',
    ]);

    expect($data->classSectionId)->toBeNull()
        ->and($data->staffId)->toBeNull()
        ->and($data->roomId)->toBeNull();
});

test('generate timetable data falls back to the configured default workload', function (): void {
    $data = new GenerateTimetableData(schoolId: 'sch_1', timetableId: 1);

    expect($data->lessonsPerWeek(5, 2))->toBe(2);
});

test('generate timetable data honors explicit requirements and floors them', function (): void {
    $data = new GenerateTimetableData(
        schoolId: 'sch_1',
        timetableId: 1,
        requirements: [5 => 3, 6 => 0],
    );

    expect($data->lessonsPerWeek(5, 2))->toBe(3)
        ->and($data->lessonsPerWeek(6, 2))->toBe(1)
        ->and($data->lessonsPerWeek(7, 2))->toBe(2);
});
