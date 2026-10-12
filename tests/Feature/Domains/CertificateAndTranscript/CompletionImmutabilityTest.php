<?php

declare(strict_types=1);

use App\Domains\CertificateAndTranscript\Application\CompletionRequestService;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionRequestStatus;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use Illuminate\Support\Str;

beforeEach(function (): void {
    $this->service = app(CompletionRequestService::class);
});

function completionImmutabilityLevel(string $schoolId): \App\Domains\Academic\Infrastructure\Models\EducationLevel
{
    return \App\Domains\Academic\Infrastructure\Models\EducationLevel::firstOrCreate(
        ['school_id' => $schoolId, 'name' => 'Collège'],
        [
            'code' => 'EL'.Str::upper(Str::random(3)),
            'sort_order' => 1,
            'status' => 'active',
        ],
    );
}

test('a generated request cannot be generated again', function (): void {
    $schoolId = makeTenant();
    $context = makeAssessmentContext($schoolId, false);
    $enrollment = enrollStudent(
        $schoolId,
        $context['year']->id,
        $context['program']->id,
        completionImmutabilityLevel($schoolId)->id,
        $context['class']->id,
    );

    $request = CompletionRequest::factory()
        ->forSchool($schoolId)
        ->forStudent($enrollment->student_id)
        ->state(fn (): array => ['completion_document_kind' => CompletionDocumentKind::Transcript])
        ->generated()
        ->create();

    expect(fn () => $this->service->generateTranscript($request))
        ->toThrow(InvalidStatusTransitionException::class);
});

test('a published request is immutable and rejects any further lifecycle action', function (): void {
    $schoolId = makeTenant();
    $request = CompletionRequest::factory()
        ->forSchool($schoolId)
        ->state(fn (): array => ['completion_document_kind' => CompletionDocumentKind::Transcript])
        ->published()
        ->create();

    expect($request->status)->toBe(CompletionRequestStatus::Published)
        ->and($request->isImmutable())->toBeTrue()
        ->and(fn () => $this->service->generateTranscript($request))
        ->toThrow(InvalidStatusTransitionException::class)
        ->and(fn () => $this->service->publishCompletionRequest($request))
        ->toThrow(InvalidStatusTransitionException::class);
});

test('an archived request stays immutable', function (): void {
    $schoolId = makeTenant();
    $request = CompletionRequest::factory()
        ->forSchool($schoolId)
        ->state(fn (): array => ['completion_document_kind' => CompletionDocumentKind::Transcript])
        ->published()
        ->create();

    $this->service->archiveCompletionRequest($request);

    expect($request->refresh()->status)->toBe(CompletionRequestStatus::Archived)
        ->and($request->refresh()->isImmutable())->toBeTrue()
        ->and(fn () => $this->service->publishCompletionRequest($request))
        ->toThrow(InvalidStatusTransitionException::class);
});
