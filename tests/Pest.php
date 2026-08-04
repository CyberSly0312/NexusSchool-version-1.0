<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)
    ->in(
        'Unit/Domains/Academic',
        'Feature/Domains/Academic',
        'Unit/Domains/Student',
        'Feature/Domains/Student',
        'Unit/Domains/Enrollment',
        'Feature/Domains/Enrollment',
        'Unit/Domains/Guardian',
        'Feature/Domains/Guardian',
    );

/**
 * Insert a tenant row and return its id. Engine entities reference school_id
 * through the tenants table, so a tenant must exist before records are created.
 */
function makeTenant(string $name = 'Test School'): string
{
    $id = 'test-'.Str::ulid()->toBase32();

    DB::table('tenants')->insert([
        'id' => $id,
        'uuid' => Str::ulid(),
        'name' => $name,
        'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
        'database' => 'school_'.Str::slug($name).'_'.Str::random(4),
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return $id;
}

/**
 * Build a minimal active academic context (program + active year + class) for
 * a school, so student actions can validate their academic assignment.
 *
 * @return array{\App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear, \App\Domains\Academic\Infrastructure\Models\SchoolClass}
 */
function makeAcademicContext(string $schoolId, string $className = '6ème A'): array
{
    $program = \App\Domains\Academic\Infrastructure\Models\AcademicProgram::create([
        'school_id' => $schoolId,
        'name' => 'Test program '.Str::random(4),
        'code' => 'TP'.Str::upper(Str::random(4)),
    ]);

    $year = \App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear::create([
        'school_id' => $schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);

    $class = \App\Domains\Academic\Infrastructure\Models\SchoolClass::create([
        'school_id' => $schoolId,
        'academic_program_id' => $program->id,
        'name' => $className,
        'code' => 'CL'.Str::upper(Str::random(3)),
        'status' => 'active',
    ]);

    return [$year, $class];
}

/**
 * Build a full enrollment context (active program + level + year + class) for
 * a school, so enrollment actions can validate their academic assignment.
 *
 * @return array{\App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear, \App\Domains\Academic\Infrastructure\Models\SchoolClass, \App\Domains\Academic\Infrastructure\Models\AcademicProgram, \App\Domains\Academic\Infrastructure\Models\EducationLevel}
 */
function makeEnrollmentContext(string $schoolId, string $className = '6ème A'): array
{
    $program = \App\Domains\Academic\Infrastructure\Models\AcademicProgram::create([
        'school_id' => $schoolId,
        'name' => 'Test program '.Str::random(4),
        'code' => 'TP'.Str::upper(Str::random(4)),
    ]);

    $level = \App\Domains\Academic\Infrastructure\Models\EducationLevel::create([
        'school_id' => $schoolId,
        'name' => 'Collège',
        'code' => 'EL'.Str::upper(Str::random(3)),
        'sort_order' => 1,
        'status' => 'active',
    ]);

    $year = \App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear::create([
        'school_id' => $schoolId,
        'name' => '2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);

    $class = \App\Domains\Academic\Infrastructure\Models\SchoolClass::create([
        'school_id' => $schoolId,
        'academic_program_id' => $program->id,
        'education_level_id' => $level->id,
        'name' => $className,
        'code' => 'CL'.Str::upper(Str::random(3)),
        'status' => 'active',
    ]);

    return [$year, $class, $program, $level];
}

/**
 * Open the next academic year for a school. Only one active year is allowed
 * per school at a time, so the current active year is archived first.
 */
function makeNextAcademicYear(
    string $schoolId,
    \App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear $currentYear,
): \App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear {
    $currentYear->update(['status' => 'archived']);

    $start = \Illuminate\Support\Carbon::parse($currentYear->start_date)->addYear();
    $end = \Illuminate\Support\Carbon::parse($currentYear->end_date)->addYear();

    return \App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear::create([
        'school_id' => $schoolId,
        'name' => $start->format('Y').'-'.$end->format('Y'),
        'start_date' => $start,
        'end_date' => $end,
        'status' => 'active',
    ]);
}
