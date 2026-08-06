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
        'Unit/Domains/Staff',
        'Feature/Domains/Staff',
        'Unit/Domains/Timetable',
        'Feature/Domains/Timetable',
        'Unit/Domains/Attendance',
        'Feature/Domains/Attendance',
        'Unit/Domains/Assessment',
        'Feature/Domains/Assessment',
        'Unit/Domains/Gradebook',
        'Feature/Domains/Gradebook',
        'Unit/Domains/ReportCard',
        'Feature/Domains/ReportCard',
        'Unit/Domains/ClassCouncil',
        'Feature/Domains/ClassCouncil',
        'Unit/Domains/AcademicCommunication',
        'Feature/Domains/AcademicCommunication',
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
 * Build the full communication context for a school: template, publication
 * policy and the publication channel registry entries. Returns the created
 * models keyed by name.
 *
 * @return array{template: \App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate, policy: \App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy, channels: array<string, \App\Domains\AcademicCommunication\Infrastructure\Models\PublicationChannel>}
 */
function makeCommunicationContext(string $schoolId, array $policyOverrides = []): array
{
    $template = \App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate::factory()
        ->forSchool($schoolId)
        ->ofType(\App\Domains\AcademicCommunication\Domain\Enums\DocumentType::ReportCard)
        ->create();

    $policy = \App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy::factory()
        ->forSchool($schoolId)
        ->ofType(\App\Domains\AcademicCommunication\Domain\Enums\DocumentType::ReportCard)
        ->create($policyOverrides);

    $channels = [];
    foreach ([\App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel::InternalPortal, \App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel::PdfExport] as $channel) {
        $channels[$channel->value] = \App\Domains\AcademicCommunication\Infrastructure\Models\PublicationChannel::query()
            ->forSchool($schoolId)
            ->where('key', $channel->value)
            ->first()
            ?? \App\Domains\AcademicCommunication\Infrastructure\Models\PublicationChannel::factory()
                ->forSchool($schoolId)
                ->forChannel($channel)
                ->create();
    }

    return compact('template', 'policy', 'channels');
}

/**
 * Create a staff user carrying the given role for a school (spatie role).
 */
function makeRoleUser(string $schoolId, string $roleName): \App\Models\User
{
    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
    $user = \App\Models\User::factory()->create(['school_id' => $schoolId]);

    return tap($user)->assignRole($role);
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

/**
 * Materialize the config-driven teaching grid (2 slots on Monday + Tuesday) for
 * a school, so generator and conflict tests have a deterministic grid.
 */
function makeTeachingGrid(string $schoolId): void
{
    foreach ([0, 1] as $day) {
        foreach ([1, 2] as $period) {
            \App\Domains\Timetable\Infrastructure\Models\TeachingPeriod::create([
                'school_id' => $schoolId,
                'day_of_week' => $day,
                'period_number' => $period,
                'starts_at' => $period === 1 ? '08:00' : '09:00',
                'ends_at' => $period === 1 ? '08:55' : '09:55',
                'duration_minutes' => 55,
                'is_active' => true,
            ]);
        }
    }
}

/**
 * Build a teacher (Staff + TeacherProfile) teaching a set of subjects.
 *
 * @param  list<int>  $subjectIds
 */
function makeTeacher(string $schoolId, array $subjectIds = [], string $firstName = 'Jean', string $lastName = 'Dupont', ?int $maxWeeklyHours = null): \App\Domains\Staff\Infrastructure\Models\Staff
{
    $staff = \App\Domains\Staff\Infrastructure\Models\Staff::create([
        'school_id' => $schoolId,
        'staff_number' => 'TCH'.Str::upper(Str::random(6)),
        'first_name' => $firstName,
        'last_name' => $lastName,
        'gender' => 'M',
        'phone_number' => '+330'.Str::random(9),
        'specialization' => 'teacher',
        'status' => 'active',
    ]);

    \App\Domains\Staff\Infrastructure\Models\TeacherProfile::create([
        'school_id' => $schoolId,
        'staff_id' => $staff->id,
        'teaching_subject_ids' => $subjectIds,
        'max_weekly_hours' => $maxWeeklyHours,
        'is_homeroom_teacher' => false,
    ]);

    return $staff;
}

/**
 * Build a minimal Timetable academic context for a school: an active year, an
 * open term, a class (with an optional section) and a subject. Returns the
 * created models keyed by name so tests can reference them.
 *
 * @return array<string, object>
 */
function makeTimetableAcademicContext(string $schoolId, bool $withSection = true): array
{
    $program = \App\Domains\Academic\Infrastructure\Models\AcademicProgram::create([
        'school_id' => $schoolId,
        'name' => 'Test program '.Str::random(4),
        'code' => 'TP'.Str::upper(Str::random(4)),
    ]);

    $year = \App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear::create([
        'school_id' => $schoolId,
        'name' => '2026-2027',
        'code' => 'AY-2026-2027',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
        'status' => 'active',
    ]);

    $term = \App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm::create([
        'school_id' => $schoolId,
        'calendar_year_id' => $year->id,
        'name' => 'Trimestre 1',
        'code' => 'T1',
        'starts_on' => '2026-09-01',
        'ends_on' => '2026-12-20',
        'status' => 'open',
    ]);

    $class = \App\Domains\Academic\Infrastructure\Models\SchoolClass::create([
        'school_id' => $schoolId,
        'academic_program_id' => $program->id,
        'name' => '6ème A',
        'code' => 'CL6A',
        'status' => 'active',
    ]);

    $section = $withSection
        ? \App\Domains\Academic\Infrastructure\Models\ClassSection::create([
            'school_id' => $schoolId,
            'school_class_id' => $class->id,
            'name' => 'A',
            'code' => 'SEC-A',
            'status' => 'active',
        ])
        : null;

    $subject = \App\Domains\Academic\Infrastructure\Models\AcademicSubject::create([
        'school_id' => $schoolId,
        'name' => 'Mathématiques',
        'code' => 'MATH',
        'abbreviation' => 'MATH',
        'status' => 'active',
    ]);

    return compact('year', 'term', 'class', 'section', 'subject', 'program');
}

/**
 * Build a draft Timetable bound to the given academic year and term.
 */
function makeTimetable(string $schoolId, int|string|null $yearId = null, int|string|null $termId = null): \App\Domains\Timetable\Infrastructure\Models\Timetable
{
    return \App\Domains\Timetable\Infrastructure\Models\Timetable::factory()
        ->forSchool($schoolId)
        ->create([
            'academic_calendar_year_id' => $yearId,
            'academic_calendar_term_id' => $termId,
        ]);
}

/**
 * Add a scheduled entry to a draft timetable through the application action.
 */
function scheduleLesson(
    string $schoolId,
    int|string $timetableId,
    int|string $subjectId,
    int|string $classId,
    int|string|null $staffId = null,
    ?string $roomId = null,
    int $day = 0,
    int $period = 1,
    string $startsAt = '08:00',
    string $endsAt = '08:55',
    ?\App\Models\User $actor = null,
): \App\Domains\Timetable\Infrastructure\Models\ScheduleEntry {
    return app(\App\Domains\Timetable\Application\Actions\CreateScheduleEntryAction::class)->execute(
        \App\Domains\Timetable\Application\DTOs\ScheduleEntryData::fromArray([
            'school_id' => $schoolId,
            'timetable_id' => $timetableId,
            'academic_subject_id' => $subjectId,
            'school_class_id' => $classId,
            'staff_id' => $staffId,
            'room_id' => $roomId,
            'day_of_week' => $day,
            'period_number' => $period,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]),
        $actor,
    );
}

/**
 * Build a published timetable context for attendance: an academic context
 * (year/term/class/subject/level), a timetable with one scheduled lesson on
 * the given day/period, then the timetable published. Returns the created
 * models keyed by name.
 *
 * @return array<string, object>
 */
function makePublishedLesson(
    string $schoolId,
    int $day = 0,
    int $period = 1,
    string $startsAt = '08:00',
    string $endsAt = '08:55',
): array {
    $academic = makeTimetableAcademicContext($schoolId);

    $level = \App\Domains\Academic\Infrastructure\Models\EducationLevel::create([
        'school_id' => $schoolId,
        'name' => 'Collège',
        'code' => 'EL'.Str::upper(Str::random(3)),
        'sort_order' => 1,
        'status' => 'active',
    ]);

    $timetable = makeTimetable($schoolId, $academic['year']->id, $academic['term']->id);

    scheduleLesson(
        $schoolId,
        $timetable->id,
        $academic['subject']->id,
        $academic['class']->id,
        day: $day,
        period: $period,
        startsAt: $startsAt,
        endsAt: $endsAt,
    );

    app(\App\Domains\Timetable\Application\Actions\PublishTimetableAction::class)->execute($timetable->id);

    return [...$academic, 'timetable' => $timetable, 'level' => $level];
}

/**
 * Enroll a new student into a class for an academic year (attendance context).
 */
function enrollStudent(
    string $schoolId,
    int|string $yearId,
    int|string $programId,
    int|string $levelId,
    int|string $classId,
): \App\Domains\Enrollment\Infrastructure\Models\Enrollment {
    $student = \App\Domains\Student\Infrastructure\Models\Student::factory()->create([
        'school_id' => $schoolId,
    ]);

    return \App\Domains\Enrollment\Infrastructure\Models\Enrollment::create([
        'school_id' => $schoolId,
        'student_id' => $student->id,
        'academic_calendar_year_id' => $yearId,
        'academic_program_id' => $programId,
        'education_level_id' => $levelId,
        'school_class_id' => $classId,
        'enrollment_number' => 'ENR-'.Str::upper(Str::random(8)),
        'enrollment_type' => \App\Domains\Enrollment\Domain\Enums\EnrollmentType::New,
        'status' => \App\Domains\Enrollment\Domain\Enums\EnrollmentStatus::Active,
        'enrolled_at' => now()->toDateString(),
    ]);
}

/**
 * A Monday in the 2026-2027 school year used by attendance fixture tests.
 */
function attendanceMonday(int $week): string
{
    return \Illuminate\Support\Carbon::parse('2026-09-07')->addWeeks($week - 1)->toDateString();
}

/**
 * Build a full Assessment context for a school: active year, open term, class
 * (with optional section), subject, program, an active teacher and an
 * assessment type. Returns the created models keyed by name.
 *
 * @return array<string, object>
 */
function makeAssessmentContext(string $schoolId, bool $withSection = true): array
{
    $context = makeTimetableAcademicContext($schoolId, $withSection);

    $teacher = makeTeacher($schoolId, [$context['subject']->id]);

    $type = \App\Domains\Assessment\Infrastructure\Models\AssessmentType::factory()
        ->forSchool($schoolId)
        ->create();

    return [...$context, 'teacher' => $teacher, 'type' => $type];
}

/**
 * Create a draft assessment bound to the given context through the canonical
 * create action, so feature tests exercise the real aggregate construction.
 */
function makeAssessment(
    string $schoolId,
    array $context,
    array $overrides = [],
): \App\Domains\Assessment\Infrastructure\Models\Assessment {
    return app(\App\Domains\Assessment\Application\AssessmentService::class)->createAssessment(
        \App\Domains\Assessment\Application\DTOs\CreateAssessmentData::fromArray(array_merge([
            'school_id' => $schoolId,
            'academic_calendar_year_id' => $context['year']->id,
            'academic_calendar_term_id' => $context['term']->id,
            'academic_subject_id' => $context['subject']->id,
            'school_class_id' => $context['class']->id,
            'class_section_id' => $context['section']?->id,
            'staff_id' => $context['teacher']->id,
            'assessment_type_id' => $context['type']->id,
            'title' => 'Devoir de contrôle '.Str::random(4),
            'maximum_score' => 20.0,
            'passing_score' => 10.0,
            'weight' => 1.0,
        ], $overrides)),
    );
}

/**
 * Record a score for one enrollment on one assessment component.
 */
function recordAssessmentScore(
    string $schoolId,
    \App\Domains\Assessment\Infrastructure\Models\Assessment $assessment,
    int|string|\App\Domains\Enrollment\Infrastructure\Models\Enrollment $enrollment,
    ?\App\Domains\Assessment\Infrastructure\Models\AssessmentComponent $component = null,
    float $score = 14.0,
    ?\App\Models\User $actor = null,
): void {
    app(\App\Domains\Assessment\Application\AssessmentService::class)->recordScore(
        \App\Domains\Assessment\Application\DTOs\RecordScoreData::fromArray([
            'school_id' => $schoolId,
            'assessment_id' => $assessment->id,
            'assessment_component_id' => $component?->id,
            'enrollment_id' => $enrollment instanceof \App\Domains\Enrollment\Infrastructure\Models\Enrollment
                ? $enrollment->id
                : $enrollment,
            'score' => $score,
        ]),
        $actor,
    );
}
