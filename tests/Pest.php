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
