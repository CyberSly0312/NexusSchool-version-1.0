<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\AcademicCommunication\Domain\Contracts\GradebookContract;
use App\Domains\Assessment\Application\AssessmentService;
use App\Domains\Gradebook\Application\DTOs\CreateGradebookData;
use App\Domains\Gradebook\Application\GradebookService;
use Illuminate\Support\Str;

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

    $this->enrollmentA = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->level->id,
        $this->context['class']->id,
    );

    $this->enrollmentB = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->level->id,
        $this->context['class']->id,
    );

    $this->gradebookService = app(GradebookService::class);
    $this->assessmentService = app(AssessmentService::class);
    $this->adapter = app(GradebookContract::class);
});

function adapterGradebookInput(string $schoolId, array $context): array
{
    return [
        'school_id' => $schoolId,
        'academic_calendar_year_id' => $context['year']->id,
        'academic_calendar_term_id' => $context['term']->id,
        'academic_subject_id' => $context['subject']->id,
        'school_class_id' => $context['class']->id,
        'class_section_id' => null,
        'staff_id' => $context['teacher']->id,
        'coefficient' => 2.0,
    ];
}

function publishAndScore(
    string $schoolId,
    array $context,
    object $assessment,
    object $enrollmentA,
    object $enrollmentB,
): void {
    $service = app(AssessmentService::class);
    $service->publishAssessment($assessment);
    recordAssessmentScore($schoolId, $assessment, $enrollmentA, score: 16.0);
    recordAssessmentScore($schoolId, $assessment, $enrollmentB, score: 12.0);
}

function publishedGradebookFixture(object $test): array
{
    $assessment = makeAssessment($test->schoolId, $test->context, [
        'class_section_id' => null,
        'weight' => 1.0,
    ]);

    publishAndScore($test->schoolId, $test->context, $assessment, $test->enrollmentA, $test->enrollmentB);

    $gradebook = $test->gradebookService->createGradebook(
        CreateGradebookData::fromArray(adapterGradebookInput($test->schoolId, $test->context)),
    );

    $test->gradebookService->calculateGradebook($gradebook);
    $test->gradebookService->lockGradebook($gradebook);
    $test->gradebookService->publishGradebook($gradebook);

    return ['gradebook' => $gradebook, 'assessment' => $assessment];
}

test('studentGrades maps published entries onto contract references', function (): void {
    $fixture = publishedGradebookFixture($this);
    $student = $this->enrollmentA->student;

    $grades = $this->adapter->studentGrades(
        $this->schoolId,
        $student->id,
        $this->context['year']->id,
        $this->context['term']->id,
    );

    expect($grades)->toHaveCount(1);

    $grade = $grades[0];

    expect($grade->subject->id)->toBe($this->context['subject']->id)
        ->and($grade->subject->coefficient)->toBe(2.0)
        ->and($grade->average)->toBe(16.0)
        ->and($grade->coefficient)->toBe(2.0)
        ->and($grade->weightedAverage)->toBe(32.0)
        ->and($grade->letterGrade)->toBe('A-')
        ->and($grade->appreciation)->toBe('Très bien')
        ->and($grade->rank)->toBe(1)
        ->and($grade->assessmentBreakdown)->toBe([]);

    expect($fixture['gradebook']->status->value)->toBe('published');
});

test('studentGrades returns an empty array for a student with no entry', function (): void {
    publishedGradebookFixture($this);

    $outsider = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->level->id,
        $this->context['class']->id,
    );

    $grades = $this->adapter->studentGrades(
        $this->schoolId,
        $outsider->student->id,
        $this->context['year']->id,
        $this->context['term']->id,
    );

    expect($grades)->toBe([]);
});

test('classSummary computes the class average and pass rate', function (): void {
    publishedGradebookFixture($this);

    $summary = $this->adapter->classSummary(
        $this->schoolId,
        $this->context['class']->id,
        $this->context['year']->id,
        $this->context['term']->id,
    );

    expect($summary)->not->toBeNull()
        ->and($summary->class->id)->toBe($this->context['class']->id)
        ->and($summary->term->id)->toBe($this->context['term']->id)
        ->and($summary->classAverage)->toBe(14.0)
        ->and($summary->passRate)->toBe(100.0)
        ->and($summary->entries)->toHaveCount(2);

    $first = $summary->entries[0];

    expect($first->subject->id)->toBe($this->context['subject']->id)
        ->and($first->rank)->toBe(1)
        ->and($first->average)->toBe(16.0);
});

test('classSummary returns null when nothing has been published', function (): void {
    $summary = $this->adapter->classSummary(
        $this->schoolId,
        $this->context['class']->id,
        $this->context['year']->id,
        $this->context['term']->id,
    );

    expect($summary)->toBeNull();
});

test('draft gradebooks are invisible to the communication engine', function (): void {
    $assessment = makeAssessment($this->schoolId, $this->context, ['class_section_id' => null]);
    $this->assessmentService->publishAssessment($assessment);
    recordAssessmentScore($this->schoolId, $assessment, $this->enrollmentA, score: 16.0);

    $gradebook = $this->gradebookService->createGradebook(
        CreateGradebookData::fromArray(adapterGradebookInput($this->schoolId, $this->context)),
    );

    $this->gradebookService->calculateGradebook($gradebook);

    expect($this->adapter->studentGrades(
        $this->schoolId,
        $this->enrollmentA->student->id,
        $this->context['year']->id,
        $this->context['term']->id,
    ))->toBe([]);
});
