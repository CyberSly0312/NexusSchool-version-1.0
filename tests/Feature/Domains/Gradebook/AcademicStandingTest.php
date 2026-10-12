<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\Gradebook\Application\DTOs\CreateGradebookData;
use App\Domains\Gradebook\Application\GradebookService;
use App\Domains\Gradebook\Application\Services\AcademicStandingService;
use App\Domains\Gradebook\Domain\Enums\AcademicStanding;
use App\Domains\Gradebook\Domain\References\StandingContributionReference;
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

    $this->service = app(GradebookService::class);
    $this->standing = app(AcademicStandingService::class);
});

function publishSubjectGradebook(string $schoolId, array $context, object $subject, object $enrollment, float $coefficient, float $score): void
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

function publishedContributions(string $schoolId, object $enrollment, array $subjects): array
{
    $contributions = [];

    foreach ($subjects as $subject) {
        $entry = \App\Domains\Gradebook\Infrastructure\Models\GradeEntry::query()
            ->where('school_id', $schoolId)
            ->where('student_id', $enrollment->student_id)
            ->whereHas('gradebook', fn ($query) => $query->where('academic_subject_id', $subject->id))
            ->where('status', \App\Domains\Gradebook\Domain\Enums\GradeEntryStatus::Published)
            ->firstOrFail();

        $contributions[] = new StandingContributionReference(
            percentage: (float) $entry->percentage,
            gradePoint: (float) $entry->grade_point,
            coefficient: (float) $entry->gradebook->coefficient,
        );
    }

    return $contributions;
}

test('the GPA, overall percentage and standing aggregate published gradebooks', function (): void {
    publishSubjectGradebook($this->schoolId, $this->context, $this->context['subject'], $this->enrollment, coefficient: 2.0, score: 16.0);
    publishSubjectGradebook($this->schoolId, $this->context, $this->subjectB, $this->enrollment, coefficient: 1.0, score: 12.0);

    $contributions = publishedContributions($this->schoolId, $this->enrollment, [$this->context['subject'], $this->subjectB]);

    $gpa = $this->standing->gpa($contributions);
    $percentage = $this->standing->overallPercentage($contributions);
    $standing = $this->standing->standing($contributions, $this->schoolId);

    expect($gpa->gpa)->toBe(round((3.7 * 2 + 3.0 * 1) / 3, 2))
        ->and($percentage)->toBe(round((80.0 * 2 + 60.0 * 1) / 3, 2))
        ->and($standing)->toBe(AcademicStanding::Pass);
});

test('only published gradebooks feed the standing', function (): void {
    $assessment = makeAssessment($this->schoolId, $this->context, ['class_section_id' => null]);
    recordAssessmentScore($this->schoolId, $assessment, $this->enrollment, score: 16.0);

    $gradebook = $this->service->createGradebook(CreateGradebookData::fromArray([
        'school_id' => $this->schoolId,
        'academic_calendar_year_id' => $this->context['year']->id,
        'academic_calendar_term_id' => $this->context['term']->id,
        'academic_subject_id' => $this->context['subject']->id,
        'school_class_id' => $this->context['class']->id,
        'class_section_id' => null,
        'staff_id' => $this->context['teacher']->id,
        'coefficient' => 2.0,
    ]));
    $this->service->calculateGradebook($gradebook);

    expect($this->standing->gpa([]))->toBeNull()
        ->and($this->standing->standing([], $this->schoolId))->toBeNull();
});
