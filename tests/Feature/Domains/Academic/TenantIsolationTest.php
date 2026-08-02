<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\Stream;
use Illuminate\Database\QueryException;

test('entities are isolated per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    AcademicCalendarYear::create([
        'school_id' => $schoolA,
        'name' => '2026-2027 A',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]);
    AcademicCalendarYear::create([
        'school_id' => $schoolB,
        'name' => '2026-2027 B',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]);

    expect(AcademicCalendarYear::forSchool($schoolA)->count())->toBe(1)
        ->and(AcademicCalendarYear::forSchool($schoolB)->count())->toBe(1);
});

test('the same subject code is allowed across schools', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    AcademicSubject::create(['school_id' => $schoolA, 'name' => 'Mathématiques', 'code' => 'MAT']);
    AcademicSubject::create(['school_id' => $schoolB, 'name' => 'Mathématiques', 'code' => 'MAT']);

    expect(AcademicSubject::forSchool($schoolA)->count())->toBe(1)
        ->and(AcademicSubject::forSchool($schoolB)->count())->toBe(1);
});

test('the database enforces a single active year per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    AcademicCalendarYear::create([
        'school_id' => $schoolA,
        'name' => '2025-2026',
        'start_date' => '2025-09-01',
        'end_date' => '2026-07-31',
        'status' => 'active',
    ]);

    AcademicCalendarYear::create([
        'school_id' => $schoolB,
        'name' => '2025-2026 B',
        'start_date' => '2025-09-01',
        'end_date' => '2026-07-31',
        'status' => 'active',
    ]);

    AcademicCalendarYear::create([
        'school_id' => $schoolA,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);
})->throws(QueryException::class);

test('duplicate stream names are rejected within a program', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $programA = \App\Domains\Academic\Infrastructure\Models\AcademicProgram::create([
        'school_id' => $schoolA,
        'name' => 'Premier cycle',
        'code' => 'PRE',
    ]);
    $programB = \App\Domains\Academic\Infrastructure\Models\AcademicProgram::create([
        'school_id' => $schoolB,
        'name' => 'Premier cycle',
        'code' => 'PRE',
    ]);

    Stream::create([
        'school_id' => $schoolA,
        'academic_program_id' => $programA->id,
        'name' => 'Scientifique',
        'code' => 'SCI',
    ]);
    Stream::create([
        'school_id' => $schoolB,
        'academic_program_id' => $programB->id,
        'name' => 'Scientifique',
        'code' => 'SCI',
    ]);

    expect(Stream::forSchool($schoolA)->count())->toBe(1)
        ->and(Stream::forSchool($schoolB)->count())->toBe(1);
});
