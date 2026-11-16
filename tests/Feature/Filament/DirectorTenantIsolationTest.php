<?php

declare(strict_types=1);

use App\Domains\Document\Enums\DocumentType;
use App\Domains\School\Models\School;
use App\Domains\StudentEnrollment\Enums\EnrollmentStatus;
use App\Domains\StudentEnrollment\Enums\EnrollmentType;
use App\Filament\Director\Pages\DirectorDashboard;
use App\Filament\Director\Pages\DocumentCenter;
use App\Filament\Director\Widgets\AttendanceChart;
use App\Filament\Director\Widgets\DirectorStatsOverview;
use App\Filament\Director\Widgets\EnrollmentChart;
use App\Filament\Director\Widgets\PaymentsChart;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\GeneratedDocument;
use App\Models\GradeLevel;
use App\Models\Incident;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

/**
 * Build one deterministic legacy enrollment context (year, program, grade
 * level, section and class) for a school. Reused by every enrollment of that
 * school so the factory's hard-coded foreign keys cannot collide.
 *
 * @return array{academic_year_id: int, academic_class_id: int, program_id: int, grade_level_id: int, section_id: int}
 */
function directorEnrollmentContext(string $schoolId): array
{
    $year = AcademicYear::factory()->create(['school_id' => $schoolId]);
    $program = Program::factory()->create(['school_id' => $schoolId]);
    $gradeLevel = GradeLevel::factory()->forProgram($program->id)->create();
    $section = Section::factory()->forGradeLevel($gradeLevel->id)->create();
    $academicClass = AcademicClass::factory()
        ->forAcademicYear($year->id)
        ->forProgram($program->id)
        ->forGradeLevel($gradeLevel->id)
        ->forSection($section->id)
        ->create();

    return [
        'academic_year_id' => $year->id,
        'academic_class_id' => $academicClass->id,
        'program_id' => $program->id,
        'grade_level_id' => $gradeLevel->id,
        'section_id' => $section->id,
    ];
}

/**
 * @param  array{academic_year_id: int, academic_class_id: int, program_id: int, grade_level_id: int, section_id: int}  $context
 */
function createDirectorEnrollment(string $schoolId, Student $student, array $context): StudentEnrollment
{
    return StudentEnrollment::create([
        'school_id' => $schoolId,
        'student_id' => $student->id,
        ...$context,
        'enrollment_number' => 'DIR-'.str()->upper(str()->random(10)),
        'enrollment_date' => now()->toDateString(),
        'effective_date' => now()->toDateString(),
        'enrollment_type' => EnrollmentType::NewAdmission,
        'status' => EnrollmentStatus::Active,
    ]);
}

/**
 * Seed two schools with deliberately different dashboard figures so every
 * count, sum and chart dataset can be asserted in both directions.
 *
 * School A: 1 student, 1 teacher + 1 other agent, present/absent/late = 1/1/3,
 *           paid = 123 456, 2 pending payments, 1 pending document, 1 open
 *           incident, 2 enrollments.
 * School B: 3 students, 2 teachers, present/absent/late = 5/4/1,
 *           paid = 987 654, 1 pending payment, 1 pending document, 2 open
 *           incidents, 4 enrollments.
 *
 * @return array{schoolA: string, schoolB: string}
 */
function seedDirectorTenantData(): array
{
    $schoolA = makeTenant('Director School A');
    $schoolB = makeTenant('Director School B');

    $studentA = Student::factory()->create(['school_id' => $schoolA]);
    $studentsB = Student::factory()->count(3)->create(['school_id' => $schoolB]);

    makeRoleUser($schoolA, 'teacher');
    makeRoleUser($schoolA, 'accountant');
    makeRoleUser($schoolB, 'teacher');
    makeRoleUser($schoolB, 'teacher');

    $today = now()->toDateString();

    foreach (['present', 'absent', 'late', 'late', 'late'] as $status) {
        AttendanceRecord::create([
            'school_id' => $schoolA,
            'student_id' => $studentA->id,
            'status' => $status,
            'date' => $today,
        ]);
    }

    foreach (['present', 'present', 'present', 'present', 'present', 'absent', 'absent', 'absent', 'absent', 'late'] as $status) {
        AttendanceRecord::create([
            'school_id' => $schoolB,
            'student_id' => $studentsB->first()->id,
            'status' => $status,
            'date' => $today,
        ]);
    }

    Payment::create(['school_id' => $schoolA, 'student_id' => $studentA->id, 'amount' => 123456, 'payment_method' => 'cash', 'status' => 'paid', 'paid_at' => now()]);
    Payment::create(['school_id' => $schoolA, 'student_id' => $studentA->id, 'amount' => 20, 'payment_method' => 'cash', 'status' => 'pending']);
    Payment::create(['school_id' => $schoolA, 'student_id' => $studentA->id, 'amount' => 30, 'payment_method' => 'cash', 'status' => 'pending']);

    Payment::create(['school_id' => $schoolB, 'student_id' => $studentsB->first()->id, 'amount' => 987654, 'payment_method' => 'cash', 'status' => 'paid', 'paid_at' => now()]);
    Payment::create(['school_id' => $schoolB, 'student_id' => $studentsB->first()->id, 'amount' => 50, 'payment_method' => 'cash', 'status' => 'pending']);

    GeneratedDocument::create(['school_id' => $schoolA, 'type' => DocumentType::SchoolCertificate->value, 'title' => 'A signed', 'signed_at' => now()]);
    GeneratedDocument::create(['school_id' => $schoolA, 'type' => DocumentType::SchoolCertificate->value, 'title' => 'A pending']);
    GeneratedDocument::create(['school_id' => $schoolB, 'type' => DocumentType::SchoolCertificate->value, 'title' => 'B pending']);

    Incident::create(['school_id' => $schoolA, 'title' => 'A incident', 'severity' => 'low', 'status' => 'open']);
    Incident::create(['school_id' => $schoolB, 'title' => 'B incident 1', 'severity' => 'low', 'status' => 'open']);
    Incident::create(['school_id' => $schoolB, 'title' => 'B incident 2', 'severity' => 'low', 'status' => 'open']);

    $contextA = directorEnrollmentContext($schoolA);
    $contextB = directorEnrollmentContext($schoolB);

    createDirectorEnrollment($schoolA, $studentA, $contextA);
    createDirectorEnrollment($schoolA, $studentA, $contextA);

    foreach (range(1, 4) as $ignored) {
        createDirectorEnrollment($schoolB, $studentsB->first(), $contextB);
    }

    return ['schoolA' => $schoolA, 'schoolB' => $schoolB];
}

function initializeDirectorTenant(string $schoolId): void
{
    Filament::setServingStatus();
    Filament::setCurrentPanel('director');
    Tenancy::initialize(School::findOrFail($schoolId));
}

beforeEach(function (): void {
    Role::firstOrCreate(['name' => 'director', 'guard_name' => 'web']);
});

afterEach(function (): void {
    Tenancy::end();
});

// ---------------------------------------------------------------------------
// Dashboard widget aggregations
// ---------------------------------------------------------------------------

test('director widgets aggregate only school A data when school A is initialised', function (): void {
    ['schoolA' => $schoolA] = seedDirectorTenantData();

    initializeDirectorTenant($schoolA);

    $stats = directorWidgetStats(app(DirectorStatsOverview::class));
    $attendance = directorWidgetData(app(AttendanceChart::class));
    $payments = directorWidgetData(app(PaymentsChart::class));
    $enrollments = directorWidgetData(app(EnrollmentChart::class));

    $monthIndex = now()->month - 1;
    $dayIndex = now()->dayOfWeekIso - 1;

    expect($stats[0]->getValue())->toBe(1)
        ->and($stats[1]->getValue())->toBe(1)
        ->and($stats[1]->getDescription())->toBe('Sur 2 agents')
        ->and($stats[2]->getValue())->toBe(1)
        ->and($stats[2]->getDescription())->toBe('1 absent(s), 3 en retard')
        ->and($stats[3]->getValue())->toBe('123 456 FCFA')
        ->and($stats[3]->getDescription())->toBe('2 en attente')
        ->and($stats[4]->getValue())->toBe(1)
        ->and($stats[5]->getValue())->toBe(1)
        ->and($stats[6]->getValue())->toBe(1)
        ->and($attendance['datasets'][0]['data'][$dayIndex])->toBe(1)
        ->and($attendance['datasets'][1]['data'][$dayIndex])->toBe(1)
        ->and($attendance['datasets'][2]['data'][$dayIndex])->toBe(3)
        ->and($payments['datasets'][0]['data'][$monthIndex])->toBe(123456.0)
        ->and($enrollments['datasets'][0]['data'][$monthIndex])->toBe(2);
});

test('director widgets aggregate only school B data when school B is initialised', function (): void {
    ['schoolB' => $schoolB] = seedDirectorTenantData();

    initializeDirectorTenant($schoolB);

    $stats = directorWidgetStats(app(DirectorStatsOverview::class));
    $attendance = directorWidgetData(app(AttendanceChart::class));
    $payments = directorWidgetData(app(PaymentsChart::class));
    $enrollments = directorWidgetData(app(EnrollmentChart::class));

    $monthIndex = now()->month - 1;
    $dayIndex = now()->dayOfWeekIso - 1;

    expect($stats[0]->getValue())->toBe(3)
        ->and($stats[1]->getValue())->toBe(2)
        ->and($stats[1]->getDescription())->toBe('Sur 2 agents')
        ->and($stats[2]->getValue())->toBe(5)
        ->and($stats[2]->getDescription())->toBe('4 absent(s), 1 en retard')
        ->and($stats[3]->getValue())->toBe('987 654 FCFA')
        ->and($stats[3]->getDescription())->toBe('1 en attente')
        ->and($stats[4]->getValue())->toBe(1)
        ->and($stats[5]->getValue())->toBe(2)
        ->and($stats[6]->getValue())->toBe(4)
        ->and($attendance['datasets'][0]['data'][$dayIndex])->toBe(5)
        ->and($attendance['datasets'][1]['data'][$dayIndex])->toBe(4)
        ->and($attendance['datasets'][2]['data'][$dayIndex])->toBe(1)
        ->and($payments['datasets'][0]['data'][$monthIndex])->toBe(987654.0)
        ->and($enrollments['datasets'][0]['data'][$monthIndex])->toBe(4);
});

test('director widgets fail closed without an initialised tenant', function (): void {
    $school = makeTenant('Director Missing Tenant School');
    $student = Student::factory()->create(['school_id' => $school]);

    AttendanceRecord::create(['school_id' => $school, 'student_id' => $student->id, 'status' => 'present', 'date' => now()->toDateString()]);
    Payment::create(['school_id' => $school, 'student_id' => $student->id, 'amount' => 500, 'payment_method' => 'cash', 'status' => 'paid', 'paid_at' => now()]);
    createDirectorEnrollment($school, $student, directorEnrollmentContext($school));

    $attendance = directorWidgetData(app(AttendanceChart::class));
    $payments = directorWidgetData(app(PaymentsChart::class));
    $enrollments = directorWidgetData(app(EnrollmentChart::class));

    expect(tenant())->toBeNull()
        ->and(directorWidgetStats(app(DirectorStatsOverview::class)))->toBe([])
        ->and($attendance['datasets'][0]['data'])->toBe(array_fill(0, 7, 0))
        ->and($payments['datasets'][0]['data'])->toBe(array_fill(0, 12, 0.0))
        ->and($enrollments['datasets'][0]['data'])->toBe(array_fill(0, 12, 0));
});

test('the director dashboard registers only existing tenant-scoped widgets', function (): void {
    expect((new DirectorDashboard())->getWidgets())->toBe([
        DirectorStatsOverview::class,
        EnrollmentChart::class,
        AttendanceChart::class,
        PaymentsChart::class,
    ]);
});

test('the director dashboard renders for each school', function (): void {
    $schoolA = makeTenant('Director Dashboard A');
    $schoolB = makeTenant('Director Dashboard B');

    $directorA = makeRoleUser($schoolA, 'director');
    $directorB = makeRoleUser($schoolB, 'director');

    $this->actingAs($directorA)
        ->get('http://'.tenantHost($schoolA).'/director')
        ->assertSuccessful();

    // The test process keeps tenancy between requests; end it so the next
    // host can establish its own tenant.
    Tenancy::end();

    $this->actingAs($directorB)
        ->get('http://'.tenantHost($schoolB).'/director')
        ->assertSuccessful();
});

// ---------------------------------------------------------------------------
// Document center isolation
// ---------------------------------------------------------------------------

test('document center only lists the current school students as options', function (): void {
    $schoolA = makeTenant('Document Options A');
    $schoolB = makeTenant('Document Options B');

    $studentA = Student::factory()->create(['school_id' => $schoolA]);
    $studentB = Student::factory()->create(['school_id' => $schoolB]);

    Filament::setServingStatus();
    Filament::setCurrentPanel('director');

    Tenancy::initialize(School::findOrFail($schoolA));
    Livewire::test(DocumentCenter::class)
        ->assertFormFieldExists('student_id', function (Select $field) use ($studentA): bool {
            return array_keys($field->getOptions()) === [$studentA->id];
        });

    Tenancy::end();
    Tenancy::initialize(School::findOrFail($schoolB));
    Livewire::test(DocumentCenter::class)
        ->assertFormFieldExists('student_id', function (Select $field) use ($studentB): bool {
            return array_keys($field->getOptions()) === [$studentB->id];
        });

    Tenancy::end();
    Livewire::test(DocumentCenter::class)
        ->assertFormFieldExists('student_id', fn (Select $field): bool => $field->getOptions() === []);
});

test('document center rejects a student id belonging to another school', function (): void {
    $schoolA = makeTenant('Document Rejection A');
    $schoolB = makeTenant('Document Rejection B');

    Student::factory()->create(['school_id' => $schoolA]);
    $foreignStudent = Student::factory()->create(['school_id' => $schoolB]);

    initializeDirectorTenant($schoolA);

    Livewire::test(DocumentCenter::class)
        ->fillForm([
            'document_type' => DocumentType::SchoolCertificate->value,
            'student_id' => $foreignStudent->id,
            'destination' => null,
        ])
        ->call('generateDocument')
        ->assertHasFormErrors(['student_id']);

    expect(GeneratedDocument::count())->toBe(0);
});

test('document center generates a document for a same-school student', function (): void {
    $schoolA = makeTenant('Document Generation A');
    $schoolB = makeTenant('Document Generation B');

    $studentA = Student::factory()->create(['school_id' => $schoolA]);
    $foreignStudent = Student::factory()->create(['school_id' => $schoolB]);

    initializeDirectorTenant($schoolA);

    Livewire::test(DocumentCenter::class)
        ->fillForm([
            'document_type' => DocumentType::SchoolCertificate->value,
            'student_id' => $studentA->id,
            'destination' => 'Direction',
        ])
        ->call('generateDocument')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('generated_documents', [
        'school_id' => $schoolA,
        'student_id' => $studentA->id,
        'type' => DocumentType::SchoolCertificate->value,
    ]);

    expect(GeneratedDocument::count())->toBe(1)
        ->and(GeneratedDocument::where('school_id', $schoolB)->count())->toBe(0)
        ->and(GeneratedDocument::where('student_id', $foreignStudent->id)->count())->toBe(0);
});

test('document center recent documents are scoped and fail closed', function (): void {
    $schoolA = makeTenant('Document Recent A');
    $schoolB = makeTenant('Document Recent B');

    $documentA = GeneratedDocument::create(['school_id' => $schoolA, 'type' => DocumentType::SchoolCertificate->value, 'title' => 'A document']);
    $documentB = GeneratedDocument::create(['school_id' => $schoolB, 'type' => DocumentType::SchoolCertificate->value, 'title' => 'B document']);

    initializeDirectorTenant($schoolA);

    $page = Livewire::test(DocumentCenter::class)->instance();
    $recent = $page->getRecentDocuments();

    expect($recent)->toHaveCount(1)
        ->and($recent->first()->is($documentA))->toBeTrue()
        ->and($recent->contains($documentB))->toBeFalse();

    Tenancy::end();

    expect($page->getRecentDocuments())->toBeEmpty();
});

test('document center only renders the current school documents over http', function (): void {
    $schoolA = makeTenant('Document Http A');
    $schoolB = makeTenant('Document Http B');

    $directorA = makeRoleUser($schoolA, 'director');
    $directorB = makeRoleUser($schoolB, 'director');

    $documentA = GeneratedDocument::create(['school_id' => $schoolA, 'type' => DocumentType::SchoolCertificate->value, 'title' => 'A document']);
    $documentB = GeneratedDocument::create(['school_id' => $schoolB, 'type' => DocumentType::SchoolCertificate->value, 'title' => 'B document']);

    $this->actingAs($directorA)
        ->get('http://'.tenantHost($schoolA).'/director/document-center')
        ->assertSuccessful()
        ->assertSee($documentA->reference_number)
        ->assertDontSee($documentB->reference_number);

    Tenancy::end();

    $this->actingAs($directorB)
        ->get('http://'.tenantHost($schoolB).'/director/document-center')
        ->assertSuccessful()
        ->assertSee($documentB->reference_number)
        ->assertDontSee($documentA->reference_number);

    Tenancy::end();

    // The host is authoritative: another school's director cannot open this page.
    $this->actingAs($directorA)
        ->get('http://'.tenantHost($schoolB).'/director/document-center')
        ->assertForbidden();
});
