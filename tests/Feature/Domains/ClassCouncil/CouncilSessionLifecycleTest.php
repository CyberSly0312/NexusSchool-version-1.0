<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\ClassCouncil\Application\ClassCouncilService;
use App\Domains\ClassCouncil\Application\DTOs\CreateCouncilSessionData;
use App\Domains\ClassCouncil\Application\DTOs\RecordCouncilDecisionData;
use App\Domains\ClassCouncil\Application\DTOs\RecordCouncilDeliberationData;
use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionCategory;
use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType;
use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\ClassCouncil\Domain\Events\CouncilDecisionRecorded;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionArchived;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionConvened;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionCreated;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionPublished;
use App\Domains\ClassCouncil\Domain\Exceptions\ClassCouncilException;
use App\Domains\ClassCouncil\Domain\Exceptions\CouncilSessionLockedException;
use App\Domains\ClassCouncil\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domains\ClassCouncil\Domain\Exceptions\OverrideApprovalRequiredException;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDecision;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Domains\Gradebook\Application\DTOs\CreateGradebookData;
use App\Domains\Gradebook\Application\GradebookService;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

function classCouncilLevel(string $schoolId): EducationLevel
{
    return EducationLevel::firstOrCreate(
        ['school_id' => $schoolId, 'name' => 'Collège'],
        [
            'code' => 'EL'.Str::upper(Str::random(3)),
            'sort_order' => 1,
            'status' => 'active',
        ],
    );
}

/**
 * Publish a fully graded gradebook for one subject, so the council sees a real
 * standing and an automatic decision.
 */
function classCouncilSubjectGradebook(string $schoolId, array $context, object $subject, object $enrollment, float $coefficient, float $score): void
{
    $assessment = makeAssessment($schoolId, $context, [
        'class_section_id' => null,
        'academic_subject_id' => $subject->id,
    ]);
    app(\App\Domains\Assessment\Application\AssessmentService::class)->publishAssessment($assessment);

    recordAssessmentScore($schoolId, $assessment, $enrollment, score: $score);

    $gradebook = app(GradebookService::class)->createGradebook(CreateGradebookData::fromArray([
        'school_id' => $schoolId,
        'academic_calendar_year_id' => $context['year']->id,
        'academic_calendar_term_id' => $context['term']->id,
        'academic_subject_id' => $subject->id,
        'school_class_id' => $context['class']->id,
        'class_section_id' => null,
        'staff_id' => $context['teacher']->id,
        'coefficient' => $coefficient,
    ]));

    app(GradebookService::class)->calculateGradebook($gradebook);
    app(GradebookService::class)->lockGradebook($gradebook);
    app(GradebookService::class)->publishGradebook($gradebook);
}

function classCouncilData(string $schoolId, array $context, ?int $sectionId = null): CreateCouncilSessionData
{
    return CreateCouncilSessionData::fromArray([
        'school_id' => $schoolId,
        'academic_calendar_year_id' => $context['year']->id,
        'academic_calendar_term_id' => $context['term']->id,
        'school_class_id' => $context['class']->id,
        'class_section_id' => $sectionId,
    ]);
}

function classCouncilDecisionData(string $schoolId, object $session, object $enrollment, array $overrides = []): RecordCouncilDecisionData
{
    return RecordCouncilDecisionData::fromArray(array_merge([
        'school_id' => $schoolId,
        'council_session_id' => $session->id,
        'student_id' => $enrollment->student_id,
        'decision_type' => CouncilDecisionType::Passed->value,
        'category' => CouncilDecisionCategory::Promotion->value,
        'label' => CouncilDecisionType::Passed->label(),
        'summary' => 'Résultats conformes',
    ], $overrides));
}

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makeAssessmentContext($this->schoolId, false);

    $this->level = classCouncilLevel($this->schoolId);
    $this->enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->level->id,
        $this->context['class']->id,
    );

    $this->subjectB = AcademicSubject::create([
        'school_id' => $this->schoolId,
        'name' => 'Français',
        'code' => 'FRA'.Str::upper(Str::random(2)),
        'abbreviation' => 'FRA',
        'status' => 'active',
    ]);

    $this->service = app(ClassCouncilService::class);
});

test('a council session is created as a draft over a validated academic frame', function (): void {
    Event::fake([CouncilSessionCreated::class]);

    $session = $this->service->createSession(classCouncilData($this->schoolId, $this->context));

    expect($session)->toBeInstanceOf(CouncilSession::class)
        ->and($session->school_id)->toBe($this->schoolId)
        ->and($session->status)->toBe(CouncilSessionStatus::Draft)
        ->and($session->reference)->not->toBeNull()
        ->and($session->uuid)->not->toBeNull();

    Event::assertDispatched(CouncilSessionCreated::class);
});

test('a session refuses an academic frame owned by another school', function (): void {
    $otherSchool = makeTenant();

    expect(fn () => $this->service->createSession(CreateCouncilSessionData::fromArray([
        'school_id' => $otherSchool,
        'academic_calendar_year_id' => $this->context['year']->id,
        'academic_calendar_term_id' => $this->context['term']->id,
        'school_class_id' => $this->context['class']->id,
    ])))->toThrow(ClassCouncilException::class);
});

test('convening builds the review list from the published standing', function (): void {
    Event::fake([CouncilSessionConvened::class]);

    $session = $this->service->createSession(classCouncilData($this->schoolId, $this->context));
    classCouncilSubjectGradebook($this->schoolId, $this->context, $this->context['subject'], $this->enrollment, 2.0, 16.0);
    classCouncilSubjectGradebook($this->schoolId, $this->context, $this->subjectB, $this->enrollment, 1.0, 12.0);

    $reviews = $this->service->conveneSession($session);

    expect($session->refresh()->status)->toBe(CouncilSessionStatus::Convened)
        ->and($session->refresh()->held_at)->not->toBeNull()
        ->and($reviews)->toHaveCount(1)
        ->and($reviews[0]->student->id)->toBe($this->enrollment->student_id)
        ->and($reviews[0]->standing)->not->toBeNull()
        ->and($reviews[0]->automatic)->not->toBeNull()
        ->and($reviews[0]->automatic->type)->toBe(CouncilDecisionType::Passed);

    Event::assertDispatched(CouncilSessionConvened::class);
});

test('a decision is recorded for a convened session with a sequential code', function (): void {
    Event::fake([CouncilDecisionRecorded::class]);

    $session = $this->service->createSession(classCouncilData($this->schoolId, $this->context));
    $this->service->conveneSession($session);

    $decision = $this->service->recordDecision(
        classCouncilDecisionData($this->schoolId, $session, $this->enrollment),
    );

    expect($decision)->toBeInstanceOf(CouncilDecision::class)
        ->and($decision->school_id)->toBe($this->schoolId)
        ->and($decision->student_id)->toBe($this->enrollment->student_id)
        ->and($decision->decision_type)->toBe(CouncilDecisionType::Passed)
        ->and($decision->code)->toBe(sprintf('%s-001', $session->reference))
        ->and($decision->decision_date)->not->toBeNull()
        ->and($decision->uuid)->not->toBeNull();

    Event::assertDispatched(CouncilDecisionRecorded::class);
});

test('a decision cannot be recorded while the session is not convened', function (): void {
    $session = $this->service->createSession(classCouncilData($this->schoolId, $this->context));

    expect(fn () => $this->service->recordDecision(
        classCouncilDecisionData($this->schoolId, $session, $this->enrollment),
    ))->toThrow(CouncilSessionLockedException::class);
});

test('a decision refuses a student who is not enrolled in the council class', function (): void {
    $session = $this->service->createSession(classCouncilData($this->schoolId, $this->context));
    $this->service->conveneSession($session);

    expect(fn () => $this->service->recordDecision(
        classCouncilDecisionData($this->schoolId, $session, $this->enrollment, [
            'student_id' => 999_999,
        ]),
    ))->toThrow(ClassCouncilException::class);
});

test('an override requires a justification', function (): void {
    $session = $this->service->createSession(classCouncilData($this->schoolId, $this->context));
    $this->service->conveneSession($session);

    expect(fn () => $this->service->recordDecision(
        classCouncilDecisionData($this->schoolId, $session, $this->enrollment, [
            'decision_type' => CouncilDecisionType::Failed->value,
            'label' => CouncilDecisionType::Failed->label(),
            'is_override' => true,
            'approved_by' => 1,
        ]),
    ))->toThrow(OverrideApprovalRequiredException::class);
});

test('an override requires an approval', function (): void {
    $session = $this->service->createSession(classCouncilData($this->schoolId, $this->context));
    $this->service->conveneSession($session);

    expect(fn () => $this->service->recordDecision(
        classCouncilDecisionData($this->schoolId, $session, $this->enrollment, [
            'decision_type' => CouncilDecisionType::Failed->value,
            'label' => CouncilDecisionType::Failed->label(),
            'is_override' => true,
            'justification' => 'Échec non représentatif du niveau réel',
        ]),
    ))->toThrow(OverrideApprovalRequiredException::class);
});

test('an approved override snapshots the automatic decision being overridden', function (): void {
    $session = $this->service->createSession(classCouncilData($this->schoolId, $this->context));
    classCouncilSubjectGradebook($this->schoolId, $this->context, $this->context['subject'], $this->enrollment, 2.0, 16.0);
    $this->service->conveneSession($session);

    $approver = makeRoleUser($this->schoolId, 'director');

    $decision = $this->service->recordDecision(
        classCouncilDecisionData($this->schoolId, $session, $this->enrollment, [
            'decision_type' => CouncilDecisionType::Failed->value,
            'label' => CouncilDecisionType::Failed->label(),
            'is_override' => true,
            'justification' => 'Échec non représentatif du niveau réel',
            'approved_by' => $approver->id,
        ]),
    );

    expect($decision->is_override)->toBeTrue()
        ->and($decision->automatic_decision_type)->toBe(CouncilDecisionType::Passed->value)
        ->and($decision->automatic_decision_label)->toBe(CouncilDecisionType::Passed->label())
        ->and($decision->justification)->not->toBeNull()
        ->and($decision->approved_by)->toBe($approver->id)
        ->and($decision->approved_at)->not->toBeNull();
});

test('a deliberation record stays confidential inside the council', function (): void {
    $session = $this->service->createSession(classCouncilData($this->schoolId, $this->context));
    $this->service->conveneSession($session);

    $decision = $this->service->recordDecision(
        classCouncilDecisionData($this->schoolId, $session, $this->enrollment),
    );

    $deliberation = $this->service->recordDeliberation(RecordCouncilDeliberationData::fromArray([
        'school_id' => $this->schoolId,
        'council_session_id' => $session->id,
        'council_decision_id' => $decision->id,
        'student_id' => $this->enrollment->student_id,
        'member' => 'Mme la Proviseure',
        'topic' => 'Dossier élève',
        'summary' => 'Échanges sur le parcours de l’élève.',
    ]));

    expect($deliberation->summary)->toBe('Échanges sur le parcours de l’élève.')
        ->and($session->deliberations()->count())->toBe(1);
});

test('publishing freezes the session and its decisions', function (): void {
    Event::fake([CouncilSessionPublished::class]);

    $session = $this->service->createSession(classCouncilData($this->schoolId, $this->context));
    $this->service->conveneSession($session);
    $this->service->recordDecision(classCouncilDecisionData($this->schoolId, $session, $this->enrollment));

    $published = $this->service->publishSession($session);

    expect($published->status)->toBe(CouncilSessionStatus::Published)
        ->and($published->published_at)->not->toBeNull();

    expect(fn () => $this->service->recordDecision(
        classCouncilDecisionData($this->schoolId, $published, $this->enrollment),
    ))->toThrow(CouncilSessionLockedException::class);

    Event::assertDispatched(CouncilSessionPublished::class);
});

test('a published session can be archived for audit', function (): void {
    Event::fake([CouncilSessionArchived::class]);

    $session = $this->service->createSession(classCouncilData($this->schoolId, $this->context));
    $this->service->conveneSession($session);
    $published = $this->service->publishSession($session);

    $this->service->archiveSession($published);

    expect($published->refresh()->status)->toBe(CouncilSessionStatus::Archived)
        ->and($published->refresh()->archived_at)->not->toBeNull();

    Event::assertDispatched(CouncilSessionArchived::class);
});

test('a draft session cannot be published or archived', function (): void {
    $session = $this->service->createSession(classCouncilData($this->schoolId, $this->context));

    expect(fn () => $this->service->publishSession($session))
        ->toThrow(InvalidStatusTransitionException::class)
        ->and(fn () => $this->service->archiveSession($session))
        ->toThrow(InvalidStatusTransitionException::class);
});

test('every council operation writes its audit trail', function (): void {
    $session = $this->service->createSession(classCouncilData($this->schoolId, $this->context));
    classCouncilSubjectGradebook($this->schoolId, $this->context, $this->context['subject'], $this->enrollment, 2.0, 16.0);
    $this->service->conveneSession($session);

    $approver = makeRoleUser($this->schoolId, 'director');
    $this->service->recordDecision(classCouncilDecisionData($this->schoolId, $session, $this->enrollment, [
        'decision_type' => CouncilDecisionType::Failed->value,
        'label' => CouncilDecisionType::Failed->label(),
        'is_override' => true,
        'justification' => 'Échec non représentatif',
        'approved_by' => $approver->id,
    ]));
    $this->service->publishSession($session);

    $actions = AuditLog::query()
        ->where('auditable_type', CouncilSession::class)
        ->where('auditable_id', $session->id)
        ->pluck('action')
        ->all();

    expect($actions)->toContain('council_session_created')
        ->and($actions)->toContain('council_session_convened')
        ->and($actions)->toContain('council_decision_recorded')
        ->and($actions)->toContain('council_session_published');

    $overrideLog = AuditLog::query()
        ->where('auditable_type', CouncilSession::class)
        ->where('auditable_id', $session->id)
        ->where('action', 'council_decision_recorded')
        ->first();

    expect($overrideLog->new_values['is_override'])->toBeTrue()
        ->and($overrideLog->new_values['justification'])->toBe('Échec non représentatif');
});
