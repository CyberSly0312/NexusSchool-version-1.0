<?php

declare(strict_types=1);

use App\Domains\Enrollment\Infrastructure\Models\Enrollment;

test('enrollments are isolated per school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    Enrollment::factory()->count(2)->create(['school_id' => $schoolA]);
    Enrollment::factory()->create(['school_id' => $schoolB]);

    expect(Enrollment::forSchool($schoolA)->count())->toBe(2)
        ->and(Enrollment::forSchool($schoolB)->count())->toBe(1);
});

test('the same enrollment number is allowed across schools', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    Enrollment::factory()->create(['school_id' => $schoolA, 'enrollment_number' => 'ENR-SHARED-1']);
    Enrollment::factory()->create(['school_id' => $schoolB, 'enrollment_number' => 'ENR-SHARED-1']);

    expect(Enrollment::forSchool($schoolA)->where('enrollment_number', 'ENR-SHARED-1')->exists())->toBeTrue()
        ->and(Enrollment::forSchool($schoolB)->where('enrollment_number', 'ENR-SHARED-1')->exists())->toBeTrue();
});

test('the database rejects a duplicate enrollment number within one school', function (): void {
    $school = makeTenant();

    Enrollment::factory()->create(['school_id' => $school, 'enrollment_number' => 'ENR-DUP-1']);
    Enrollment::factory()->create(['school_id' => $school, 'enrollment_number' => 'ENR-DUP-1']);
})->throws(Illuminate\Database\QueryException::class);

test('the active scope only returns active enrollments', function (): void {
    $school = makeTenant();

    Enrollment::factory()->active()->create(['school_id' => $school]);
    Enrollment::factory()->suspended()->create(['school_id' => $school]);
    Enrollment::factory()->withdrawn()->create(['school_id' => $school]);

    expect(Enrollment::active()->count())->toBe(1)
        ->and(Enrollment::withTrashed()->count())->toBe(3);
});

test('the status scopes filter by lifecycle state', function (): void {
    $school = makeTenant();

    Enrollment::factory()->create(['school_id' => $school, 'status' => 'archived']);
    Enrollment::factory()->graduated()->create(['school_id' => $school]);

    expect(Enrollment::archived()->count())->toBe(1)
        ->and(Enrollment::byStatus('graduated')->count())->toBe(1);
});
