<?php

declare(strict_types=1);

use App\Domains\Assessment\Application\AssessmentService;
use App\Domains\Assessment\Application\DTOs\RecordScoreData;
use App\Domains\Assessment\Domain\Exceptions\StudentNotEnrolledException;
use App\Domains\Assessment\Domain\Exceptions\WeightTotalExceededException;

beforeEach(function (): void {
    $this->service = app(AssessmentService::class);
});

test('weight policy is enforced independently per school', function (): void {
    $schoolA = makeTenant('School A');
    $schoolB = makeTenant('School B');

    $contextA = makeAssessmentContext($schoolA);
    $contextB = makeAssessmentContext($schoolB);

    makeAssessment($schoolA, $contextA, ['weight' => 99.0]);

    $assessmentB = makeAssessment($schoolB, $contextB, ['weight' => 99.0]);

    expect($assessmentB->weight)->toBe(99.0);
});

test('weight overflow in one school does not leak into another school', function (): void {
    $schoolA = makeTenant('School A');
    $schoolB = makeTenant('School B');

    $contextA = makeAssessmentContext($schoolA);
    $contextB = makeAssessmentContext($schoolB);

    makeAssessment($schoolA, $contextA, ['weight' => 99.0]);

    makeAssessment($schoolA, $contextA, ['weight' => 2.0]);
})->throws(WeightTotalExceededException::class);

test('a student of school B cannot be graded in school A', function (): void {
    $schoolA = makeTenant('School A');
    $schoolB = makeTenant('School B');

    $contextA = makeAssessmentContext($schoolA);
    $contextB = makeAssessmentContext($schoolB);

    $foreignEnrollment = enrollStudent(
        $schoolB,
        $contextB['year']->id,
        $contextB['program']->id,
        $contextB['level']->id,
        $contextB['class']->id,
    );

    $assessmentA = makeAssessment($schoolA, $contextA);

    $this->service->recordScore(
        RecordScoreData::fromArray([
            'school_id' => $schoolA,
            'assessment_id' => $assessmentA->id,
            'enrollment_id' => $foreignEnrollment->id,
            'score' => 14.0,
        ]),
    );
})->throws(StudentNotEnrolledException::class);

test('statistics rows carry the school id of their assessment', function (): void {
    $schoolA = makeTenant('School A');
    $schoolB = makeTenant('School B');

    $contextA = makeAssessmentContext($schoolA);

    $enrollment = enrollStudent(
        $schoolA,
        $contextA['year']->id,
        $contextA['program']->id,
        $contextA['level']->id,
        $contextA['class']->id,
    );

    $assessmentA = makeAssessment($schoolA, $contextA);
    $this->service->publishAssessment($assessmentA);
    recordAssessmentScore($schoolA, $assessmentA->refresh(), $enrollment, score: 12.0);

    $this->service->generateStatistics($assessmentA->refresh());

    $statistics = $assessmentA->refresh()->statistics()->first();

    expect($statistics->school_id)->toBe($schoolA)
        ->and(\App\Domains\Assessment\Infrastructure\Models\AssessmentStatistics::query()
            ->forSchool($schoolA)
            ->count())->toBe(1)
        ->and(\App\Domains\Assessment\Infrastructure\Models\AssessmentStatistics::query()
            ->forSchool($schoolB)
            ->count())->toBe(0);
});
