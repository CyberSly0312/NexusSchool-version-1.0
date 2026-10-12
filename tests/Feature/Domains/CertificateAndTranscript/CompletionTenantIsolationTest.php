<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\CertificateAndTranscript\Application\CompletionRequestService;
use App\Domains\CertificateAndTranscript\Application\DTOs\CreateCompletionRequestData;
use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Domain\Exceptions\CompletionRequestException;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use Illuminate\Support\Str;

beforeEach(function (): void {
    $this->service = app(CompletionRequestService::class);
});

function completionIsolationLevel(string $schoolId): EducationLevel
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

function completionIsolationRequestData(string $schoolId, int|string $studentId): CreateCompletionRequestData
{
    return CreateCompletionRequestData::fromArray([
        'school_id' => $schoolId,
        'student_id' => $studentId,
        'completion_document_kind' => CompletionDocumentKind::Transcript->value,
    ]);
}

test('completion requests do not leak across tenants', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextA = makeAssessmentContext($schoolA, false);
    $enrollment = enrollStudent(
        $schoolA,
        $contextA['year']->id,
        $contextA['program']->id,
        completionIsolationLevel($schoolA)->id,
        $contextA['class']->id,
    );

    $requestA = $this->service->createCompletionRequest(completionIsolationRequestData($schoolA, $enrollment->student_id));

    expect($requestA->school_id)->toBe($schoolA)
        ->and(CompletionRequest::query()->forSchool($schoolA)->count())->toBe(1)
        ->and(CompletionRequest::query()->forSchool($schoolB)->count())->toBe(0);
});

test('a request refuses a student owned by another tenant', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextA = makeAssessmentContext($schoolA, false);
    $enrollment = enrollStudent(
        $schoolA,
        $contextA['year']->id,
        $contextA['program']->id,
        completionIsolationLevel($schoolA)->id,
        $contextA['class']->id,
    );

    expect(fn () => $this->service->createCompletionRequest(completionIsolationRequestData($schoolB, $enrollment->student_id)))
        ->toThrow(CompletionRequestException::class);
});

test('completion request entries are scoped to their request school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $requestA = CompletionRequest::factory()->forSchool($schoolA)->create();
    $requestB = CompletionRequest::factory()->forSchool($schoolB)->create();

    \App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequestEntry::factory()
        ->forSchool($schoolA)
        ->forRequest($requestA->getKey())
        ->create();

    \App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequestEntry::factory()
        ->forSchool($schoolB)
        ->forRequest($requestB->getKey())
        ->create();

    expect($requestA->entries()->count())->toBe(1)
        ->and($requestB->entries()->count())->toBe(1)
        ->and($requestA->entries()->count())->toBe(1);
});
