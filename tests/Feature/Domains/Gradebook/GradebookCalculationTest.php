<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\Assessment\Application\AssessmentService;
use App\Domains\Gradebook\Application\DTOs\CreateGradebookData;
use App\Domains\Gradebook\Application\GradebookService;
use Illuminate\Support\Str;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makeAssessmentContext($this->schoolId, false);

    $this->level = EducationLevel::create([
        'school_id' => $this->schoolId,
        'name' => 'CollÃ¨ge',
        'code' => 'EL'.Str::upper(Str::random(3)),
        'sort_order' => 1,
        'status' => 'active',
    ]);

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

    $this->service = app(GradebookService::class);
    $this->assessmentService = app(AssessmentService::class);
});

function gradebookInput(string $schoolId, array $context): array
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

function publishAssessmentWithWeight(string $schoolId, array $context, float $weight): \App\Domains\Assessment\Infrastructure\Models\Assessment
{
    $service = app(AssessmentService::class);

    $assessment = makeAssessment($schoolId, $context, [
        'class_section_id' => null,
        'weight' => $weight,
    ]);

    $service->publishAssessment($assessment);

    return $assessment;
}

test('the term average is the weighted mean of the assessment percentages', function (): void {
    $assessment = publishAssessmentWithWeight($this->schoolId, $this->context, 1.0);
    recordAssessmentScore($this->schoolId, $assessment, $this->enrollmentA, score: 16.0);
    recordAssessmentScore($this->schoolId, $assessment, $this->enrollmentB, score: 12.0);

    $gradebook = $this->service->createGradebook(CreateGradebookData::fromArray(gradebookInput($this->schoolId, $this->context)));
    $this->service->calculateGradebook($gradebook);

    $entries = $gradebook->entries()->orderBy('percentage', 'desc')->get();

    expect($entries)->toHaveCount(2);

    $best = $entries->first();
    $worst = $entries->last();

    expect($best->percentage)->toBe(80.0)
        ->and($best->average)->toBe(16.0)
        ->and($best->letter_grade)->toBe('A-')
        ->and($best->rank)->toBe(1)
        ->and($best->rank_denominator)->toBe(2)
        ->and($worst->percentage)->toBe(60.0)
        ->and($worst->average)->toBe(12.0)
        ->and($worst->letter_grade)->toBe('B')
        ->and($worst->rank)->toBe(2);
});

test('assessment weights tilt the term average', function (): void {
    $first = publishAssessmentWithWeight($this->schoolId, $this->context, 1.0);
    $second = publishAssessmentWithWeight($this->schoolId, $this->context, 3.0);

    recordAssessmentScore($this->schoolId, $first, $this->enrollmentA, score: 10.0);
    recordAssessmentScore($this->schoolId, $first, $this->enrollmentB, score: 10.0);

    recordAssessmentScore($this->schoolId, $second, $this->enrollmentA, score: 14.0);
    recordAssessmentScore($this->schoolId, $second, $this->enrollmentB, score: 14.0);

    $gradebook = $this->service->createGradebook(CreateGradebookData::fromArray(gradebookInput($this->schoolId, $this->context)));
    $this->service->calculateGradebook($gradebook);

    $entry = $gradebook->entries()->where('enrollment_id', $this->enrollmentA->id)->first();

    expect($entry->percentage)->toBe(round((50 * 1 + 70 * 3) / 4, 2))
        ->and($entry->average)->toBe(round($entry->percentage / 5, 2));
});

test('students without any published contribution receive no entry', function (): void {
    $gradebook = $this->service->createGradebook(CreateGradebookData::fromArray(gradebookInput($this->schoolId, $this->context)));
    $this->service->calculateGradebook($gradebook);

    expect($gradebook->entries()->count())->toBe(0);
});

test('a draft assessment is excluded from the term average', function (): void {
    $assessment = makeAssessment($this->schoolId, $this->context, ['class_section_id' => null]);
    recordAssessmentScore($this->schoolId, $assessment, $this->enrollmentA, score: 16.0);

    $gradebook = $this->service->createGradebook(CreateGradebookData::fromArray(gradebookInput($this->schoolId, $this->context)));
    $this->service->calculateGradebook($gradebook);

    expect($gradebook->entries()->count())->toBe(0);
});

test('recalculation refreshes the term average as contributions change', function (): void {
    $first = publishAssessmentWithWeight($this->schoolId, $this->context, 1.0);
    recordAssessmentScore($this->schoolId, $first, $this->enrollmentA, score: 16.0);
    recordAssessmentScore($this->schoolId, $first, $this->enrollmentB, score: 12.0);

    $gradebook = $this->service->createGradebook(CreateGradebookData::fromArray(gradebookInput($this->schoolId, $this->context)));
    $this->service->calculateGradebook($gradebook);

    $entry = $gradebook->entries()->where('enrollment_id', $this->enrollmentA->id)->first();
    expect($entry->percentage)->toBe(80.0);

    $second = publishAssessmentWithWeight($this->schoolId, $this->context, 1.0);
    recordAssessmentScore($this->schoolId, $second, $this->enrollmentA, score: 10.0);
    recordAssessmentScore($this->schoolId, $second, $this->enrollmentB, score: 10.0);

    $this->service->calculateGradebook($gradebook);

    expect($gradebook->entries()->where('enrollment_id', $this->enrollmentA->id)->first()->percentage)->toBe(65.0);
});

test('equal percentages share the same rank', function (): void {
    $assessment = publishAssessmentWithWeight($this->schoolId, $this->context, 1.0);
    recordAssessmentScore($this->schoolId, $assessment, $this->enrollmentA, score: 14.0);
    recordAssessmentScore($this->schoolId, $assessment, $this->enrollmentB, score: 14.0);

    $gradebook = $this->service->createGradebook(CreateGradebookData::fromArray(gradebookInput($this->schoolId, $this->context)));
    $this->service->calculateGradebook($gradebook);

    $ranks = $gradebook->entries()->pluck('rank')->sort()->values();

    expect($ranks->first())->toBe(1)
        ->and($ranks->last())->toBe(1);
});
