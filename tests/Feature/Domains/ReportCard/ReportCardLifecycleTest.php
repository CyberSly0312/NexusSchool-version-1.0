<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Events\DocumentGenerated;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationRecord;
use App\Domains\Gradebook\Application\DTOs\CreateGradebookData;
use App\Domains\Gradebook\Application\GradebookService;
use App\Domains\ReportCard\Application\DTOs\CreateReportCardData;
use App\Domains\ReportCard\Application\ReportCardService;
use App\Domains\ReportCard\Domain\Enums\ReportCardEntryStatus;
use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;
use App\Domains\ReportCard\Domain\Enums\ReportCardType;
use App\Domains\ReportCard\Domain\Events\ReportCardArchived;
use App\Domains\ReportCard\Domain\Events\ReportCardCreated;
use App\Domains\ReportCard\Domain\Events\ReportCardGenerated;
use App\Domains\ReportCard\Domain\Events\ReportCardPublished;
use App\Domains\ReportCard\Domain\Events\ReportCardRegenerated;
use App\Domains\ReportCard\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domains\ReportCard\Domain\Exceptions\ReportCardException;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\Support\FakeRenderer;

function reportCardLevel(string $schoolId): EducationLevel
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
 * Publish a fully graded gradebook for one subject, so the bulletin has a real
 * grade line and a real standing.
 */
function reportCardSubjectGradebook(string $schoolId, array $context, object $subject, object $enrollment, float $coefficient, float $score): void
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

function reportCardData(string $schoolId, array $context, ?int $sectionId = null): CreateReportCardData
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

    $this->level = reportCardLevel($this->schoolId);
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

    $this->comm = makeCommunicationContext($this->schoolId, ['channels' => [DocumentChannel::InternalPortal->value]]);
    makeRoleUser($this->schoolId, 'director');

    $this->app->instance(DocumentRendererContract::class, new FakeRenderer());

    $this->service = app(ReportCardService::class);
});

test('a report card run is created as a draft over a validated academic frame', function (): void {
    Event::fake([ReportCardCreated::class]);

    $reportCard = $this->service->createReportCard(reportCardData($this->schoolId, $this->context));

    expect($reportCard)->toBeInstanceOf(ReportCard::class)
        ->and($reportCard->school_id)->toBe($this->schoolId)
        ->and($reportCard->status)->toBe(ReportCardStatus::Draft)
        ->and($reportCard->report_card_type)->toBe(ReportCardType::Term)
        ->and($reportCard->uuid)->not->toBeNull()
        ->and($reportCard->entries()->count())->toBe(0);

    Event::assertDispatched(ReportCardCreated::class);
});

test('the scope is unique per school, year, term, class and section', function (): void {
    $first = $this->service->createReportCard(reportCardData($this->schoolId, $this->context));

    expect(fn () => $this->service->createReportCard(reportCardData($this->schoolId, $this->context)))
        ->toThrow(ReportCardException::class);

    expect(ReportCard::query()->forSchool($this->schoolId)->count())->toBe(1)
        ->and($first->status)->toBe(ReportCardStatus::Draft);
});

test('generation snapshots every active student and delegates the official document', function (): void {
    Event::fake([ReportCardGenerated::class, DocumentGenerated::class]);

    $reportCard = $this->service->createReportCard(reportCardData($this->schoolId, $this->context));
    reportCardSubjectGradebook($this->schoolId, $this->context, $this->context['subject'], $this->enrollment, 2.0, 16.0);
    reportCardSubjectGradebook($this->schoolId, $this->context, $this->subjectB, $this->enrollment, 1.0, 12.0);

    $generated = $this->service->generateReportCard($reportCard);

    expect($generated->status)->toBe(ReportCardStatus::Generated)
        ->and($generated->generated_at)->not->toBeNull()
        ->and($generated->entries()->count())->toBe(1);

    $entry = $generated->entries()->first();
    expect($entry->status)->toBe(ReportCardEntryStatus::Included)
        ->and($entry->student_id)->toBe($this->enrollment->student_id)
        ->and($entry->academic_document_id)->not->toBeNull()
        ->and($entry->academic_document_number)->not->toBeNull();

    $document = AcademicDocument::query()->findOrFail($entry->academic_document_id);
    expect($document->status)->toBe(DocumentStatus::Generated)
        ->and($document->origin)->toBe(DocumentOrigin::ReportCard)
        ->and($document->student_id)->toBe($this->enrollment->student_id)
        ->and($document->content)->not->toBeEmpty();

    Event::assertDispatched(ReportCardGenerated::class);
    Event::assertDispatched(DocumentGenerated::class);
});

test('the bulletin snapshot carries the validated gradebook and standing figures', function (): void {
    $reportCard = $this->service->createReportCard(reportCardData($this->schoolId, $this->context));
    reportCardSubjectGradebook($this->schoolId, $this->context, $this->context['subject'], $this->enrollment, 2.0, 16.0);
    reportCardSubjectGradebook($this->schoolId, $this->context, $this->subjectB, $this->enrollment, 1.0, 12.0);

    $generated = $this->service->generateReportCard($reportCard);
    $content = $generated->entries()->first()->content;

    expect($content['student']['full_name'])->not->toBeNull()
        ->and($content['period']['year_id'])->toBe($this->context['year']->id)
        ->and($content['period']['term_id'])->toBe($this->context['term']->id)
        ->and($content['period']['class_id'])->toBe($this->context['class']->id)
        ->and($content['gradebook'])->toHaveCount(2)
        ->and($content['standing']['academic_standing'])->not->toBeNull()
        ->and($content['standing']['gpa'])->toBeFloat()
        ->and($content['standing']['overall_percentage'])->toBeFloat()
        ->and($content['policies'])->toBe([])
        ->and($content['council'])->toBe([]);
});

test('a draft run cannot be published or archived', function (): void {
    $reportCard = $this->service->createReportCard(reportCardData($this->schoolId, $this->context));

    expect(fn () => $this->service->publishReportCard($reportCard))
        ->toThrow(InvalidStatusTransitionException::class)
        ->and(fn () => $this->service->archiveReportCard($reportCard))
        ->toThrow(InvalidStatusTransitionException::class);
});

test('a generated run cannot be regenerated from draft', function (): void {
    $reportCard = $this->service->createReportCard(reportCardData($this->schoolId, $this->context));

    expect(fn () => $this->service->regenerateReportCard($reportCard))
        ->toThrow(ReportCardException::class);
});

test('publishing delivers every bulletin through the communication engine', function (): void {
    Event::fake([ReportCardPublished::class]);

    $reportCard = $this->service->createReportCard(reportCardData($this->schoolId, $this->context));
    reportCardSubjectGradebook($this->schoolId, $this->context, $this->context['subject'], $this->enrollment, 2.0, 16.0);
    $generated = $this->service->generateReportCard($reportCard);

    $published = $this->service->publishReportCard($generated);

    expect($published->status)->toBe(ReportCardStatus::Published)
        ->and($published->published_at)->not->toBeNull()
        ->and(PublicationRecord::query()->forSchool($this->schoolId)->count())->toBeGreaterThan(0);

    $document = AcademicDocument::query()->findOrFail($generated->entries()->first()->academic_document_id);
    expect($document->status)->toBe(DocumentStatus::Published);

    Event::assertDispatched(ReportCardPublished::class);
});

test('a published run can be archived for audit', function (): void {
    Event::fake([ReportCardArchived::class]);

    $reportCard = $this->service->createReportCard(reportCardData($this->schoolId, $this->context));
    reportCardSubjectGradebook($this->schoolId, $this->context, $this->context['subject'], $this->enrollment, 2.0, 16.0);
    $generated = $this->service->generateReportCard($reportCard);
    $published = $this->service->publishReportCard($generated);

    $this->service->archiveReportCard($published);

    expect($published->refresh()->status)->toBe(ReportCardStatus::Archived)
        ->and($published->refresh()->archived_at)->not->toBeNull();

    Event::assertDispatched(ReportCardArchived::class);
});

test('regenerating a published run recomposes fresh bulletins and documents', function (): void {
    Event::fake([ReportCardRegenerated::class]);

    $reportCard = $this->service->createReportCard(reportCardData($this->schoolId, $this->context));
    reportCardSubjectGradebook($this->schoolId, $this->context, $this->context['subject'], $this->enrollment, 2.0, 16.0);
    $generated = $this->service->generateReportCard($reportCard);
    $published = $this->service->publishReportCard($generated);

    $previousDocument = $published->entries()->first()->academic_document_id;

    $regenerated = $this->service->regenerateReportCard($published);

    expect($regenerated->status)->toBe(ReportCardStatus::Generated)
        ->and($regenerated->entries()->count())->toBe(1)
        ->and($regenerated->entries()->first()->academic_document_id)->not->toBe($previousDocument);

    Event::assertDispatched(ReportCardRegenerated::class);
});
