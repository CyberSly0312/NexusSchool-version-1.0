<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\Gradebook\Application\DTOs\CreateGradebookData;
use App\Domains\Gradebook\Application\GradebookService;
use App\Domains\RecordsLifecycle\Domain\Enums\RecordLifecycleState;
use App\Domains\RecordsLifecycle\Infrastructure\Models\RecordLifecycle;
use App\Domains\ReportCard\Application\DTOs\CreateReportCardData;
use App\Domains\ReportCard\Application\ReportCardService;
use App\Domains\ReportCard\Domain\Enums\ReportCardType;
use Illuminate\Support\Str;
use Tests\Support\FakeRenderer;

function rcLifecycleSubjectGradebook(string $schoolId, array $context, object $subject, object $enrollment, float $coefficient, float $score): void
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

function rcLifecycleReportCardData(string $schoolId, array $context, ?int $sectionId = null): CreateReportCardData
{
    return CreateReportCardData::fromArray([
        'school_id' => $schoolId,
        'academic_calendar_year_id' => $context['year']->id,
        'academic_calendar_term_id' => $context['term']->id,
        'school_class_id' => $context['class']->id,
        'class_section_id' => $sectionId,
        'report_card_type' => ReportCardType::Term->value,
    ]);
}

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makeAssessmentContext($this->schoolId, false);

    $this->level = EducationLevel::firstOrCreate(
        ['school_id' => $this->schoolId, 'name' => 'Collège'],
        [
            'code' => 'EL'.Str::upper(Str::random(3)),
            'sort_order' => 1,
            'status' => 'active',
        ],
    );
    $this->enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->level->id,
        $this->context['class']->id,
    );

    makeCommunicationContext($this->schoolId, ['channels' => [DocumentChannel::InternalPortal->value]]);
    makeRoleUser($this->schoolId, 'director');

    $this->app->instance(DocumentRendererContract::class, new FakeRenderer());
    $this->service = app(ReportCardService::class);

    rcLifecycleSubjectGradebook(
        $this->schoolId,
        $this->context,
        $this->context['subject'],
        $this->enrollment,
        2.0,
        16.0,
    );
});

test('report card generation registers a lifecycle envelope with correct provenance', function (): void {
    $reportCard = $this->service->createReportCard(rcLifecycleReportCardData($this->schoolId, $this->context));

    $generated = $this->service->generateReportCard($reportCard);

    $lifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'report_card')
        ->where('source_type', 'report_card')
        ->where('source_id', (string) $generated->getKey())
        ->first();

    expect($lifecycle)->not->toBeNull()
        ->and($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Registered)
        ->and($lifecycle->school_id)->toBe($this->schoolId)
        ->and($lifecycle->source_version)->toBe($generated->uuid)
        ->and($lifecycle->origin_event)->toBe('ReportCardGenerated')
        ->and($lifecycle->academic_context)->toBe([
            'year_id' => $reportCard->academic_calendar_year_id,
            'term_id' => $reportCard->academic_calendar_term_id,
            'class_id' => $reportCard->school_class_id,
            'section_id' => $reportCard->class_section_id,
        ]);
});

test('report card publication transitions the lifecycle envelope to published', function (): void {
    $reportCard = $this->service->createReportCard(rcLifecycleReportCardData($this->schoolId, $this->context));
    $generated = $this->service->generateReportCard($reportCard);

    $this->service->publishReportCard($generated);

    $lifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'report_card')
        ->where('source_type', 'report_card')
        ->where('source_id', (string) $generated->getKey())
        ->first();

    expect($lifecycle)->not->toBeNull()
        ->and($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Published)
        ->and($lifecycle->published_at)->not->toBeNull();
});

test('report card archival transitions the lifecycle envelope to archived', function (): void {
    $reportCard = $this->service->createReportCard(rcLifecycleReportCardData($this->schoolId, $this->context));
    $generated = $this->service->generateReportCard($reportCard);

    $this->service->archiveReportCard($generated);

    $lifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'report_card')
        ->where('source_type', 'report_card')
        ->where('source_id', (string) $generated->getKey())
        ->first();

    expect($lifecycle)->not->toBeNull()
        ->and($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Archived)
        ->and($lifecycle->archived_at)->not->toBeNull();
});

test('report card publish-then-archive transitions lifecycle through published to archived', function (): void {
    $reportCard = $this->service->createReportCard(rcLifecycleReportCardData($this->schoolId, $this->context));
    $generated = $this->service->generateReportCard($reportCard);

    $this->service->publishReportCard($generated);
    $this->service->archiveReportCard($generated);

    $lifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'report_card')
        ->where('source_type', 'report_card')
        ->where('source_id', (string) $generated->getKey())
        ->first();

    expect($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Archived)
        ->and($lifecycle->published_at)->not->toBeNull()
        ->and($lifecycle->archived_at)->not->toBeNull();
});

test('lifecycle records from different schools remain isolated', function (): void {
    $otherSchool = makeTenant('Other School');
    $otherContext = makeAssessmentContext($otherSchool, false);

    $otherLevel = EducationLevel::firstOrCreate(
        ['school_id' => $otherSchool, 'name' => 'Collège'],
        [
            'code' => 'EL'.Str::upper(Str::random(3)),
            'sort_order' => 1,
            'status' => 'active',
        ],
    );
    $otherEnrollment = enrollStudent(
        $otherSchool,
        $otherContext['year']->id,
        $otherContext['program']->id,
        $otherLevel->id,
        $otherContext['class']->id,
    );

    makeCommunicationContext($otherSchool, ['channels' => [DocumentChannel::InternalPortal->value]]);
    makeRoleUser($otherSchool, 'director');

    rcLifecycleSubjectGradebook(
        $otherSchool,
        $otherContext,
        $otherContext['subject'],
        $otherEnrollment,
        2.0,
        14.0,
    );

    $reportCardA = $this->service->createReportCard(rcLifecycleReportCardData($this->schoolId, $this->context));
    $generatedA = $this->service->generateReportCard($reportCardA);

    $reportCardB = $this->service->createReportCard(rcLifecycleReportCardData($otherSchool, $otherContext));
    $generatedB = $this->service->generateReportCard($reportCardB);

    $schoolALifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'report_card')
        ->where('source_type', 'report_card')
        ->first();

    $schoolBLifecycle = RecordLifecycle::query()
        ->forSchool($otherSchool)
        ->where('source_domain', 'report_card')
        ->where('source_type', 'report_card')
        ->first();

    expect($schoolALifecycle)->not->toBeNull()
        ->and($schoolBLifecycle)->not->toBeNull()
        ->and($schoolALifecycle->school_id)->toBe($this->schoolId)
        ->and($schoolBLifecycle->school_id)->toBe($otherSchool)
        ->and($schoolALifecycle->source_id)->not->toBe($schoolBLifecycle->source_id);
});
