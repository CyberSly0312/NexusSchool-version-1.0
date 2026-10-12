<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationRecord;
use App\Domains\CertificateAndTranscript\Application\CompletionRequestService;
use App\Domains\CertificateAndTranscript\Application\DTOs\CreateCompletionRequestData;
use App\Domains\CertificateAndTranscript\Domain\Enums\CertificateEligibilityVerdict;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestEntryStatus;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestStatus;
use App\Domains\CertificateAndTranscript\Domain\Events\CertificateEligibilityDetermined;
use App\Domains\CertificateAndTranscript\Domain\Events\CertificateGenerated;
use App\Domains\CertificateAndTranscript\Domain\Events\CompletionRequestArchived;
use App\Domains\CertificateAndTranscript\Domain\Events\CompletionRequestCreated;
use App\Domains\CertificateAndTranscript\Domain\Events\CompletionRequestPublished;
use App\Domains\CertificateAndTranscript\Domain\Events\TranscriptGenerated;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\CertificateIneligibilityException;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\CompletionRequestException;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\UnpublishedEvidenceException;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequestEntry;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDecision;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Domains\Enrollment\Domain\Enums\GraduationDecision;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentGraduation;
use App\Domains\Gradebook\Application\DTOs\CreateGradebookData;
use App\Domains\Gradebook\Application\GradebookService;
use App\Domains\ReportCard\Application\DTOs\CreateReportCardData;
use App\Domains\ReportCard\Application\ReportCardService;
use App\Domains\ReportCard\Domain\Enums\ReportCardType;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\Support\FakeRenderer;

function completionLevel(string $schoolId): EducationLevel
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
function completionSubjectGradebook(string $schoolId, array $context, object $subject, object $enrollment, float $coefficient, float $score): void
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

function completionReportCardData(string $schoolId, array $context, ?int $sectionId = null): CreateReportCardData
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

/**
 * Run a full report-card life to generate and publish a bulletin snapshot for
 * the enrollment.
 */
function completionPublishedReportCard(string $schoolId, array $context, object $enrollment, object $subjectB)
{
    $service = app(ReportCardService::class);

    $reportCard = $service->createReportCard(completionReportCardData($schoolId, $context));
    completionSubjectGradebook($schoolId, $context, $context['subject'], $enrollment, 2.0, 16.0);
    completionSubjectGradebook($schoolId, $context, $subjectB, $enrollment, 1.0, 12.0);

    return $service->publishReportCard($service->generateReportCard($reportCard));
}

function completionRequestData(string $schoolId, int|string $studentId, CompletionDocumentKind $kind): CreateCompletionRequestData
{
    return CreateCompletionRequestData::fromArray([
        'school_id' => $schoolId,
        'student_id' => $studentId,
        'completion_document_kind' => $kind->value,
    ]);
}

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makeAssessmentContext($this->schoolId, false);

    $this->level = completionLevel($this->schoolId);
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

    AcademicDocumentTemplate::factory()->forSchool($this->schoolId)->ofType(DocumentType::Transcript)->create();
    AcademicDocumentTemplate::factory()->forSchool($this->schoolId)->ofType(DocumentType::Certificate)->create();
    PublicationPolicy::factory()->forSchool($this->schoolId)->ofType(DocumentType::Transcript)->create([
        'channels' => [DocumentChannel::InternalPortal->value],
    ]);
    PublicationPolicy::factory()->forSchool($this->schoolId)->ofType(DocumentType::Certificate)->create([
        'channels' => [DocumentChannel::InternalPortal->value],
    ]);

    makeRoleUser($this->schoolId, 'director');

    $this->app->instance(DocumentRendererContract::class, new FakeRenderer());

    $this->service = app(CompletionRequestService::class);
});

test('a completion request is created as a draft for one student and document kind', function (): void {
    Event::fake([CompletionRequestCreated::class]);

    $request = $this->service->createCompletionRequest(completionRequestData(
        $this->schoolId,
        $this->enrollment->student_id,
        CompletionDocumentKind::Transcript,
    ));

    expect($request)->toBeInstanceOf(CompletionRequest::class)
        ->and($request->school_id)->toBe($this->schoolId)
        ->and($request->status)->toBe(CompletionRequestStatus::Draft)
        ->and($request->completion_document_kind)->toBe(CompletionDocumentKind::Transcript)
        ->and($request->uuid)->not->toBeNull()
        ->and($request->entries()->count())->toBe(0);

    Event::assertDispatched(CompletionRequestCreated::class);
});

test('a transcript is generated verbatim from the published bulletin snapshots', function (): void {
    Event::fake([TranscriptGenerated::class]);

    $reportCard = completionPublishedReportCard($this->schoolId, $this->context, $this->enrollment, $this->subjectB);

    $request = $this->service->createCompletionRequest(completionRequestData(
        $this->schoolId,
        $this->enrollment->student_id,
        CompletionDocumentKind::Transcript,
    ));

    $generated = $this->service->generateTranscript($request);

    expect($generated->status)->toBe(CompletionRequestStatus::Generated)
        ->and($generated->generated_at)->not->toBeNull()
        ->and($generated->entries()->count())->toBe(1);

    $entry = $generated->entries()->first();
    expect($entry)->toBeInstanceOf(CompletionRequestEntry::class)
        ->and($entry->status)->toBe(CompletionRequestEntryStatus::Issued)
        ->and($entry->academic_document_id)->not->toBeNull()
        ->and($entry->academic_document_type)->toBe(CompletionDocumentKind::Transcript)
        ->and($entry->content['periods'])->toHaveCount(1)
        ->and($entry->content['periods'][0]['snapshot'])->toEqual($reportCard->entries()->first()->content)
        ->and($entry->content['partial'])->toBeFalse();

    $document = AcademicDocument::query()->findOrFail($entry->academic_document_id);
    expect($document->status)->toBe(DocumentStatus::Generated)
        ->and($document->origin)->toBe(DocumentOrigin::Transcript)
        ->and($document->student_id)->toBe($this->enrollment->student_id)
        ->and($document->content_version)->toBe(1);

    Event::assertDispatched(TranscriptGenerated::class);
});

test('a transcript cannot be generated without any published bulletin', function (): void {
    $request = $this->service->createCompletionRequest(completionRequestData(
        $this->schoolId,
        $this->enrollment->student_id,
        CompletionDocumentKind::Transcript,
    ));

    expect(fn () => $this->service->generateTranscript($request))
        ->toThrow(UnpublishedEvidenceException::class)
        ->and($request->refresh()->status)->toBe(CompletionRequestStatus::Draft);
});

test('a draft request cannot be published or archived', function (): void {
    $request = $this->service->createCompletionRequest(completionRequestData(
        $this->schoolId,
        $this->enrollment->student_id,
        CompletionDocumentKind::Transcript,
    ));

    expect(fn () => $this->service->publishCompletionRequest($request))
        ->toThrow(InvalidStatusTransitionException::class)
        ->and(fn () => $this->service->archiveCompletionRequest($request))
        ->toThrow(InvalidStatusTransitionException::class);
});

test('publishing delivers the completion document through the communication engine', function (): void {
    Event::fake([CompletionRequestPublished::class]);

    completionPublishedReportCard($this->schoolId, $this->context, $this->enrollment, $this->subjectB);

    $request = $this->service->createCompletionRequest(completionRequestData(
        $this->schoolId,
        $this->enrollment->student_id,
        CompletionDocumentKind::Transcript,
    ));
    $generated = $this->service->generateTranscript($request);

    $published = $this->service->publishCompletionRequest($generated);

    expect($published->status)->toBe(CompletionRequestStatus::Published)
        ->and($published->published_at)->not->toBeNull()
        ->and(PublicationRecord::query()->forSchool($this->schoolId)->count())->toBeGreaterThan(0);

    $document = AcademicDocument::query()->findOrFail($generated->entries()->first()->academic_document_id);
    expect($document->status)->toBe(DocumentStatus::Published);

    Event::assertDispatched(CompletionRequestPublished::class);
});

test('a published request can be archived for audit', function (): void {
    Event::fake([CompletionRequestArchived::class]);

    completionPublishedReportCard($this->schoolId, $this->context, $this->enrollment, $this->subjectB);

    $request = $this->service->createCompletionRequest(completionRequestData(
        $this->schoolId,
        $this->enrollment->student_id,
        CompletionDocumentKind::Transcript,
    ));
    $generated = $this->service->generateTranscript($request);
    $published = $this->service->publishCompletionRequest($generated);

    $this->service->archiveCompletionRequest($published);

    expect($published->refresh()->status)->toBe(CompletionRequestStatus::Archived)
        ->and($published->refresh()->archived_at)->not->toBeNull();

    Event::assertDispatched(CompletionRequestArchived::class);
});

test('an eligible student receives a certificate from the published evidence', function (): void {
    Event::fake([CertificateEligibilityDetermined::class, CertificateGenerated::class]);

    completionPublishedReportCard($this->schoolId, $this->context, $this->enrollment, $this->subjectB);

    $session = CouncilSession::factory()->forSchool($this->schoolId)->published()->create([
        'academic_calendar_year_id' => $this->context['year']->id,
        'academic_calendar_term_id' => $this->context['term']->id,
        'school_class_id' => $this->context['class']->id,
        'class_section_id' => null,
    ]);
    CouncilDecision::factory()->forSchool($this->schoolId)->forSession($session->getKey())->create([
        'student_id' => $this->enrollment->student_id,
    ]);
    EnrollmentGraduation::factory()->state(['school_id' => $this->schoolId])->create([
        'student_id' => $this->enrollment->student_id,
        'enrollment_id' => $this->enrollment->id,
        'decision' => GraduationDecision::Graduated,
    ]);

    $request = $this->service->createCompletionRequest(completionRequestData(
        $this->schoolId,
        $this->enrollment->student_id,
        CompletionDocumentKind::Certificate,
    ));

    $generated = $this->service->generateCertificate($request);

    expect($generated->status)->toBe(CompletionRequestStatus::Generated)
        ->and($generated->eligibility_verdict)->toBe(CertificateEligibilityVerdict::Eligible)
        ->and($generated->eligibility_reasons)->toBe([])
        ->and($generated->evidence_citations)->toHaveCount(3)
        ->and($generated->entries()->count())->toBe(1);

    $entry = $generated->entries()->first();
    $document = AcademicDocument::query()->findOrFail($entry->academic_document_id);
    expect($document->origin)->toBe(DocumentOrigin::Certificate)
        ->and($document->content['eligibility']['verdict'])->toBe('eligible')
        ->and($document->content['graduation']['graduation_id'])->not->toBeNull()
        ->and($document->content['evidence_citations'])->toHaveCount(3);

    Event::assertDispatched(CertificateEligibilityDetermined::class);
    Event::assertDispatched(CertificateGenerated::class);
});

test('an ineligible student is refused a certificate and keeps an auditable verdict', function (): void {
    Event::fake([CertificateEligibilityDetermined::class]);

    completionPublishedReportCard($this->schoolId, $this->context, $this->enrollment, $this->subjectB);

    $request = $this->service->createCompletionRequest(completionRequestData(
        $this->schoolId,
        $this->enrollment->student_id,
        CompletionDocumentKind::Certificate,
    ));

    expect(fn () => $this->service->generateCertificate($request))
        ->toThrow(CertificateIneligibilityException::class);

    $request->refresh();

    expect($request->status)->toBe(CompletionRequestStatus::Draft)
        ->and($request->eligibility_verdict)->toBe(CertificateEligibilityVerdict::Ineligible)
        ->and($request->eligibility_reasons)->toHaveCount(2)
        ->and($request->evidence_citations)->toHaveCount(1)
        ->and($request->entries()->count())->toBe(0);

    Event::assertDispatched(CertificateEligibilityDetermined::class);
});

test('the wrong generation action refuses the mismatched document kind', function (): void {
    completionPublishedReportCard($this->schoolId, $this->context, $this->enrollment, $this->subjectB);

    $request = $this->service->createCompletionRequest(completionRequestData(
        $this->schoolId,
        $this->enrollment->student_id,
        CompletionDocumentKind::Certificate,
    ));

    expect(fn () => $this->service->generateTranscript($request))
        ->toThrow(CompletionRequestException::class);
});
