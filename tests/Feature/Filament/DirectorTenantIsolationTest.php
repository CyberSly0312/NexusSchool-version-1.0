<?php

declare(strict_types=1);

use App\Domains\School\Models\School;
use App\Domains\StudentEnrollment\Enums\EnrollmentStatus;
use App\Domains\StudentEnrollment\Enums\EnrollmentType;
use App\Filament\Director\Widgets\AttendanceChart;
use App\Filament\Director\Widgets\DirectorStatsOverview;
use App\Filament\Director\Widgets\EnrollmentChart;
use App\Filament\Director\Widgets\PaymentsChart;
use App\Models\AttendanceRecord;
use App\Models\GeneratedDocument;
use App\Models\Incident;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function directorWidgetData(object $widget): array
{
    return (new ReflectionMethod($widget, 'getData'))->invoke($widget);
}

function directorWidgetStats(DirectorStatsOverview $widget): array
{
    return (new ReflectionMethod($widget, 'getStats'))->invoke($widget);
}

function createDirectorEnrollment(string $schoolId, Student $student): StudentEnrollment
{
    return StudentEnrollment::factory()->create([
        'school_id' => $schoolId,
        'student_id' => $student->id,
        'enrollment_number' => 'DIR-'.str()->upper(str()->random(10)),
        'enrollment_date' => now()->toDateString(),
        'effective_date' => now()->toDateString(),
        'enrollment_type' => EnrollmentType::New,
        'status' => EnrollmentStatus::Active,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

beforeEach(function (): void {
    Role::firstOrCreate(['name' => 'director', 'guard_name' => 'web']);
});

afterEach(function (): void {
    Tenancy::end();
});

test('director dashboard widgets only aggregate the initialized school records', function (): void {
    $schoolA = makeTenant('Director School A');
    $schoolB = makeTenant('Director School B');

    $studentA = Student::factory()->create(['school_id' => $schoolA]);
    $studentB = Student::factory()->count(3)->create(['school_id' => $schoolB]);

    AttendanceRecord::create(['school_id' => $schoolA, 'student_id' => $studentA->id, 'status' => 'present', 'date' => now()->toDateString()]);
    AttendanceRecord::create(['school_id' => $schoolB, 'student_id' => $studentB->first()->id, 'status' => 'absent', 'date' => now()->toDateString()]);
    AttendanceRecord::create(['school_id' => $schoolB, 'student_id' => $studentB->last()->id, 'status' => 'late', 'date' => now()->toDateString()]);

    Payment::create(['school_id' => $schoolA, 'student_id' => $studentA->id, 'amount' => 100, 'payment_method' => 'cash', 'status' => 'paid', 'paid_at' => now()]);
    Payment::create(['school_id' => $schoolA, 'student_id' => $studentA->id, 'amount' => 20, 'payment_method' => 'cash', 'status' => 'pending']);
    Payment::create(['school_id' => $schoolB, 'student_id' => $studentB->first()->id, 'amount' => 900, 'payment_method' => 'cash', 'status' => 'paid', 'paid_at' => now()]);
    Payment::create(['school_id' => $schoolB, 'student_id' => $studentB->last()->id, 'amount' => 50, 'payment_method' => 'cash', 'status' => 'pending']);

    GeneratedDocument::create(['school_id' => $schoolA, 'type' => 'certificate', 'title' => 'A document']);
    GeneratedDocument::create(['school_id' => $schoolB, 'type' => 'certificate', 'title' => 'B document']);
    Incident::create(['school_id' => $schoolA, 'title' => 'A incident', 'severity' => 'low', 'status' => 'open']);
    Incident::create(['school_id' => $schoolB, 'title' => 'B incident', 'severity' => 'low', 'status' => 'open']);

    createDirectorEnrollment($schoolA, $studentA);
    createDirectorEnrollment($schoolB, $studentB->first());
    createDirectorEnrollment($schoolB, $studentB->last());

    Tenancy::initialize(School::findOrFail($schoolA));

    $stats = directorWidgetStats(app(DirectorStatsOverview::class));
    $attendance = directorWidgetData(app(AttendanceChart::class));
    $payments = directorWidgetData(app(PaymentsChart::class));
    $enrollments = directorWidgetData(app(EnrollmentChart::class));
    $monthIndex = now()->month - 1;
    $dayIndex = now()->dayOfWeekIso - 1;

    expect($stats[0]->getValue())->toBe('1')
        ->and($stats[2]->getValue())->toBe('1')
        ->and($stats[4]->getDescription())->toBe('1 en attente')
        ->and($stats[5]->getValue())->toBe('1')
        ->and($attendance['datasets'][0]['data'][$dayIndex])->toBe(1)
        ->and($attendance['datasets'][1]['data'][$dayIndex])->toBe(0)
        ->and($attendance['datasets'][2]['data'][$dayIndex])->toBe(0)
        ->and($payments['datasets'][0]['data'][$monthIndex])->toBe(100)
        ->and($enrollments['datasets'][0]['data'][$monthIndex])->toBe(1);
});

test('director widgets fail closed without an initialized tenant', function (): void {
    $school = makeTenant('Director Missing Tenant School');
    $student = Student::factory()->create(['school_id' => $school]);

    AttendanceRecord::create(['school_id' => $school, 'student_id' => $student->id, 'status' => 'present', 'date' => now()->toDateString()]);
    Payment::create(['school_id' => $school, 'student_id' => $student->id, 'amount' => 500, 'payment_method' => 'cash', 'status' => 'paid', 'paid_at' => now()]);
    createDirectorEnrollment($school, $student);

    $attendance = directorWidgetData(app(AttendanceChart::class));
    $payments = directorWidgetData(app(PaymentsChart::class));
    $enrollments = directorWidgetData(app(EnrollmentChart::class));

    expect(directorWidgetStats(app(DirectorStatsOverview::class)))->toBe([])
        ->and($attendance['datasets'][0]['data'])->toBe(array_fill(0, 7, 0))
        ->and($payments['datasets'][0]['data'])->toBe(array_fill(0, 12, 0))
        ->and($enrollments['datasets'][0]['data'])->toBe(array_fill(0, 12, 0));
});
