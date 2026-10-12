<?php

declare(strict_types=1);

use App\Domains\AcademicPolicy\Application\DTOs\AcademicPolicyData;
use App\Domains\AcademicPolicy\Application\Services\AcademicPolicyService;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyScope;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;
use App\Domains\Assessment\Application\AssessmentService;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;

beforeEach(function (): void {
    $this->service = app(AssessmentService::class);
});

/**
 * Persist a real school-scoped policy for the assessment statistics default
 * through the Academic Policies engine, exactly as the supported workflow does.
 */
function createAssessmentDefaultPassingPolicy(string $schoolId, float $percentage): void
{
    app(AcademicPolicyService::class)->create(AcademicPolicyData::fromArray([
        'school_id' => $schoolId,
        'key' => 'academic.assessment.default_passing_percentage',
        'label' => 'Seuil de réussite par défaut — évaluations',
        'value_type' => AcademicPolicyValueType::Float->value,
        'value' => $percentage,
        'scope' => AcademicPolicyScope::School->value,
    ]));
}

/**
 * @return array<int, Enrollment>
 */
function enrollTwoStudents(string $schoolId, array $context): array
{
    return [
        enrollStudent($schoolId, $context['year']->id, $context['program']->id, $context['level']->id, $context['class']->id),
        enrollStudent($schoolId, $context['year']->id, $context['program']->id, $context['level']->id, $context['class']->id),
    ];
}

test('a real school policy changes the default passing threshold of its statistics', function (): void {
    $schoolA = makeTenant('School A');
    $contextA = makeAssessmentContext($schoolA);

    createAssessmentDefaultPassingPolicy($schoolA, 40.0);

    [$studentA, $studentB] = enrollTwoStudents($schoolA, $contextA);

    $assessment = makeAssessment($schoolA, $contextA, ['passing_score' => null]);
    $this->service->publishAssessment($assessment);
    $assessment = $assessment->refresh();

    recordAssessmentScore($schoolA, $assessment, $studentA, score: 9.0);
    recordAssessmentScore($schoolA, $assessment, $studentB, score: 11.0);

    $result = $this->service->generateStatistics($assessment);

    expect($result->passRate)->toBe(100.0);
});

test('an explicit assessment passing score still precedes the school policy', function (): void {
    $schoolA = makeTenant('School A');
    $contextA = makeAssessmentContext($schoolA);

    createAssessmentDefaultPassingPolicy($schoolA, 40.0);

    [$studentA, $studentB] = enrollTwoStudents($schoolA, $contextA);

    $assessment = makeAssessment($schoolA, $contextA, ['passing_score' => 16.0]);
    $this->service->publishAssessment($assessment);
    $assessment = $assessment->refresh();

    recordAssessmentScore($schoolA, $assessment, $studentA, score: 12.0);
    recordAssessmentScore($schoolA, $assessment, $studentB, score: 16.0);

    $result = $this->service->generateStatistics($assessment);

    expect($result->passRate)->toBe(50.0);
});

test('a school without the policy keeps the global fallback when another school defines it', function (): void {
    $schoolA = makeTenant('School A');
    $schoolB = makeTenant('School B');

    createAssessmentDefaultPassingPolicy($schoolA, 40.0);

    $contextB = makeAssessmentContext($schoolB);

    [$studentA, $studentB] = enrollTwoStudents($schoolB, $contextB);

    $assessment = makeAssessment($schoolB, $contextB, ['passing_score' => null]);
    $this->service->publishAssessment($assessment);
    $assessment = $assessment->refresh();

    recordAssessmentScore($schoolB, $assessment, $studentA, score: 9.0);
    recordAssessmentScore($schoolB, $assessment, $studentB, score: 11.0);

    $result = $this->service->generateStatistics($assessment);

    expect($result->passRate)->toBe(50.0);
});