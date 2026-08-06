<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\Assessment\Application\AssessmentService;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Gradebook\Application\DTOs\CreateGradebookData;
use App\Domains\Gradebook\Application\GradebookService;
use App\Domains\Gradebook\Domain\Enums\GradebookStatus;
use App\Domains\Gradebook\Domain\Enums\GradeEntryStatus;
use App\Domains\Gradebook\Domain\Exceptions\GradebookLockedException;
use App\Domains\Gradebook\Domain\Exceptions\InvalidStatusTransitionException;
use Illuminate\Support\Str;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makeAssessmentContext($this->schoolId, false);

    $this->level = EducationLevel::create([
        'school_id' => $this->schoolId,
        'name' => 'Collège',
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
});

function gradebookData(string $schoolId, array $context, array $overrides = []): CreateGradebookData
{
    return CreateGradebookData::fromArray(array_merge([
        'school_id' => $schoolId,
        'academic_calendar_year_id' => $context['year']->id,
        'academic_calendar_term_id' => $context['term']->id,
        'academic_subject_id' => $context['subject']->id,
        'school_class_id' => $context['class']->id,
        'class_section_id' => null,
        'staff_id' => $context['teacher']->id,
        'coefficient' => 2.0,
    ], $overrides));
}

function makePublishedAssessmentWithScores(
    string $schoolId,
    array $context,
    Enrollment $enrollmentA,
    Enrollment $enrollmentB,
    float $scoreA,
    float $scoreB,
    float $weight = 1.0,
): \App\Domains\Assessment\Infrastructure\Models\Assessment {
    $service = app(AssessmentService::class);

    $assessment = makeAssessment($schoolId, $context, [
        'class_section_id' => null,
        'weight' => $weight,
    ]);

    $service->publishAssessment($assessment);

    recordAssessmentScore($schoolId, $assessment->refresh(), $enrollmentA, score: $scoreA);
    recordAssessmentScore($schoolId, $assessment->refresh(), $enrollmentB, score: $scoreB);

    return $assessment;
}

test('a gradebook follows the forward-only lifecycle', function (): void {
    $gradebook = $this->service->createGradebook(gradebookData($this->schoolId, $this->context));

    expect($gradebook->status)->toBe(GradebookStatus::Draft);

    $this->service->calculateGradebook($gradebook);
    expect($gradebook->refresh()->status)->toBe(GradebookStatus::Calculated)
        ->and($gradebook->entries()->count())->toBe(0);

    makePublishedAssessmentWithScores($this->schoolId, $this->context, $this->enrollmentA, $this->enrollmentB, 16.0, 12.0);

    $this->service->calculateGradebook($gradebook);
    expect($gradebook->refresh()->status)->toBe(GradebookStatus::Calculated)
        ->and($gradebook->entries()->count())->toBe(2)
        ->and($gradebook->entries()->first()->status)->toBe(GradeEntryStatus::Validated);

    $this->service->lockGradebook($gradebook);
    expect($gradebook->refresh()->status)->toBe(GradebookStatus::Locked)
        ->and($gradebook->entries()->first()->status)->toBe(GradeEntryStatus::Locked);

    $this->service->publishGradebook($gradebook);
    expect($gradebook->refresh()->status)->toBe(GradebookStatus::Published)
        ->and($gradebook->entries()->first()->status)->toBe(GradeEntryStatus::Published);

    $this->service->archiveGradebook($gradebook);
    expect($gradebook->refresh()->status)->toBe(GradebookStatus::Archived);
});

test('calculating a locked gradebook is rejected', function (): void {
    $gradebook = $this->service->createGradebook(gradebookData($this->schoolId, $this->context));

    makePublishedAssessmentWithScores($this->schoolId, $this->context, $this->enrollmentA, $this->enrollmentB, 16.0, 12.0);

    $this->service->calculateGradebook($gradebook);
    $this->service->lockGradebook($gradebook);

    $this->service->calculateGradebook($gradebook);
})->throws(GradebookLockedException::class);

test('publishing requires a prior lock', function (): void {
    $gradebook = $this->service->createGradebook(gradebookData($this->schoolId, $this->context));

    makePublishedAssessmentWithScores($this->schoolId, $this->context, $this->enrollmentA, $this->enrollmentB, 16.0, 12.0);

    $this->service->calculateGradebook($gradebook);

    $this->service->publishGradebook($gradebook);
})->throws(InvalidStatusTransitionException::class);

test('unlock re-opens a locked gradebook for correction', function (): void {
    $gradebook = $this->service->createGradebook(gradebookData($this->schoolId, $this->context));

    makePublishedAssessmentWithScores($this->schoolId, $this->context, $this->enrollmentA, $this->enrollmentB, 16.0, 12.0);

    $this->service->calculateGradebook($gradebook);
    $this->service->lockGradebook($gradebook);

    $this->service->unlockGradebook($gradebook);

    expect($gradebook->refresh()->status)->toBe(GradebookStatus::Calculated)
        ->and($gradebook->locked_at)->toBeNull()
        ->and($gradebook->entries()->first()->status)->toBe(GradeEntryStatus::Validated);
});

test('duplicate gradebook scope is rejected', function (): void {
    $this->service->createGradebook(gradebookData($this->schoolId, $this->context));

    $this->service->createGradebook(gradebookData($this->schoolId, $this->context));
})->throws(\App\Domains\Gradebook\Domain\Exceptions\GradebookException::class);

test('the audit trail records the gradebook lifecycle', function (): void {
    $gradebook = $this->service->createGradebook(gradebookData($this->schoolId, $this->context));

    makePublishedAssessmentWithScores($this->schoolId, $this->context, $this->enrollmentA, $this->enrollmentB, 16.0, 12.0);

    $this->service->calculateGradebook($gradebook);
    $this->service->lockGradebook($gradebook);
    $this->service->publishGradebook($gradebook);
    $this->service->archiveGradebook($gradebook);

    $actions = \App\Models\AuditLog::query()
        ->where('school_id', $this->schoolId)
        ->where('auditable_type', \App\Domains\Gradebook\Infrastructure\Models\Gradebook::class)
        ->where('auditable_id', $gradebook->getKey())
        ->pluck('action')
        ->all();

    expect($actions)->toContain('gradebook_created')
        ->toContain('gradebook_calculated')
        ->toContain('gradebook_rankings_calculated')
        ->toContain('gradebook_locked')
        ->toContain('gradebook_published')
        ->toContain('gradebook_archived');
});
