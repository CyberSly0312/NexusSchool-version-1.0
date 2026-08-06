<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\Assessment\Application\AssessmentService;
use App\Domains\Gradebook\Application\DTOs\CreateGradebookData;
use App\Domains\Gradebook\Application\GradebookService;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Domains\Gradebook\Infrastructure\Models\GradeEntry;
use Illuminate\Support\Str;

beforeEach(function (): void {
    $this->service = app(GradebookService::class);
    $this->assessmentService = app(AssessmentService::class);
});

function tenantGradebookFixture(string $schoolId): array
{
    $context = makeAssessmentContext($schoolId, false);

    $level = EducationLevel::create([
        'school_id' => $schoolId,
        'name' => 'Collège',
        'code' => 'EL'.Str::upper(Str::random(3)),
        'sort_order' => 1,
        'status' => 'active',
    ]);

    $enrollment = enrollStudent(
        $schoolId,
        $context['year']->id,
        $context['program']->id,
        $level->id,
        $context['class']->id,
    );

    $assessment = makeAssessment($schoolId, $context, ['class_section_id' => null]);
    app(AssessmentService::class)->publishAssessment($assessment);
    recordAssessmentScore($schoolId, $assessment, $enrollment, score: 15.0);

    return compact('context', 'enrollment', 'assessment');
}

function createAndCalculateTenantGradebook(string $schoolId): Gradebook
{
    $fixture = tenantGradebookFixture($schoolId);

    $gradebook = app(GradebookService::class)->createGradebook(CreateGradebookData::fromArray([
        'school_id' => $schoolId,
        'academic_calendar_year_id' => $fixture['context']['year']->id,
        'academic_calendar_term_id' => $fixture['context']['term']->id,
        'academic_subject_id' => $fixture['context']['subject']->id,
        'school_class_id' => $fixture['context']['class']->id,
        'class_section_id' => null,
        'staff_id' => $fixture['context']['teacher']->id,
        'coefficient' => 2.0,
    ]));

    app(GradebookService::class)->calculateGradebook($gradebook);

    return $gradebook->refresh();
}

test('gradebooks do not leak across tenants', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $gradebookA = createAndCalculateTenantGradebook($schoolA);

    expect(Gradebook::query()->forSchool($schoolA)->count())->toBe(1)
        ->and(Gradebook::query()->forSchool($schoolB)->count())->toBe(0)
        ->and(Gradebook::query()->count())->toBe(1);

    expect($gradebookA->school_id)->toBe($schoolA)
        ->and($gradebookA->entries()->first()->school_id)->toBe($schoolA);
});

test('grade entries are scoped to their gradebook school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    createAndCalculateTenantGradebook($schoolA);

    expect(GradeEntry::query()->forSchool($schoolB)->count())->toBe(0)
        ->and(GradeEntry::query()->forSchool($schoolA)->count())->toBe(1);
});

test('the unique scope allows one gradebook per period subject class', function (): void {
    $schoolA = makeTenant();
    $gradebookA = createAndCalculateTenantGradebook($schoolA);

    expect(fn () => app(GradebookService::class)->createGradebook(
        CreateGradebookData::fromArray([
            'school_id' => $schoolA,
            'academic_calendar_year_id' => $gradebookA->academic_calendar_year_id,
            'academic_calendar_term_id' => $gradebookA->academic_calendar_term_id,
            'academic_subject_id' => $gradebookA->academic_subject_id,
            'school_class_id' => $gradebookA->school_class_id,
            'class_section_id' => null,
            'staff_id' => $gradebookA->staff_id,
            'coefficient' => 1.0,
        ])
    ))->toThrow(\App\Domains\Gradebook\Domain\Exceptions\GradebookException::class);
});
