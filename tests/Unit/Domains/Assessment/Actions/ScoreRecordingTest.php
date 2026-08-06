<?php

declare(strict_types=1);

use App\Domains\Assessment\Application\AssessmentService;
use App\Domains\Assessment\Application\DTOs\BulkScoreItem;
use App\Domains\Assessment\Application\DTOs\BulkScoresData;
use App\Domains\Assessment\Domain\Enums\AssessmentStatus;
use App\Domains\Assessment\Domain\Enums\ScoreStatus;
use App\Domains\Assessment\Domain\Enums\ScoreValidationStatus;
use App\Domains\Assessment\Domain\Events\AssessmentCompleted;
use App\Domains\Assessment\Domain\Events\ScoreRecorded;
use App\Domains\Assessment\Domain\Exceptions\AssessmentNotScoreableException;
use App\Domains\Assessment\Domain\Exceptions\ScoreOutOfRangeException;
use App\Domains\Assessment\Domain\Exceptions\StudentNotEnrolledException;
use App\Domains\Assessment\Domain\Exceptions\TeacherNotAuthorizedException;
use App\Domains\Assessment\Infrastructure\Models\AssessmentComponent;
use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use App\Domains\Assessment\Infrastructure\Models\AssessmentStatistics;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makeAssessmentContext($this->schoolId);
    $this->service = app(AssessmentService::class);
});

function publishedAssessment(string $schoolId, array $context): \App\Domains\Assessment\Infrastructure\Models\Assessment
{
    $assessment = makeAssessment($schoolId, $context);
    app(AssessmentService::class)->publishAssessment($assessment);

    return $assessment->refresh();
}

test('a score is recorded for an enrolled student of the class', function (): void {
    Event::fake([ScoreRecorded::class]);

    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $assessment = makeAssessment($this->schoolId, $this->context);

    recordAssessmentScore($this->schoolId, $assessment, $enrollment, score: 14.0);

    $score = $assessment->scores()->first();

    expect($score)->not->toBeNull()
        ->and($score->enrollment_id)->toBe($enrollment->id)
        ->and($score->score)->toBe(14.0)
        ->and($score->percentage)->toBe(70.0)
        ->and($score->status)->toBe(ScoreStatus::Draft)
        ->and($score->validation_status)->toBe(ScoreValidationStatus::Valid);

    Event::assertDispatched(ScoreRecorded::class);
});

test('recording the same score twice updates rather than duplicates', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $assessment = makeAssessment($this->schoolId, $this->context);

    recordAssessmentScore($this->schoolId, $assessment, $enrollment, score: 12.0);
    recordAssessmentScore($this->schoolId, $assessment, $enrollment, score: 16.0);

    expect($assessment->scores()->count())->toBe(1)
        ->and($assessment->scores()->first()->score)->toBe(16.0);
});

test('a score below the passing threshold is flagged, not rejected', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $assessment = makeAssessment($this->schoolId, $this->context);

    recordAssessmentScore($this->schoolId, $assessment, $enrollment, score: 6.0);

    expect($assessment->scores()->first()->validation_status)
        ->toBe(ScoreValidationStatus::BelowPassing);
});

test('a score outside the range is rejected', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $assessment = makeAssessment($this->schoolId, $this->context);

    recordAssessmentScore($this->schoolId, $assessment, $enrollment, score: 25.0);
})->throws(ScoreOutOfRangeException::class);

test('a negative score is rejected', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $assessment = makeAssessment($this->schoolId, $this->context);

    recordAssessmentScore($this->schoolId, $assessment, $enrollment, score: -3.0);
})->throws(ScoreOutOfRangeException::class);

test('a student not enrolled in the class cannot be graded', function (): void {
    $assessment = makeAssessment($this->schoolId, $this->context);

    recordAssessmentScore($this->schoolId, $assessment, 999999, score: 14.0);
})->throws(StudentNotEnrolledException::class);

test('a completed assessment rejects new scores', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $assessment = makeAssessment($this->schoolId, $this->context);
    $assessment->update([
        'status' => AssessmentStatus::Completed,
        'completed_at' => now(),
    ]);

    recordAssessmentScore($this->schoolId, $assessment, $enrollment, score: 14.0);
})->throws(AssessmentNotScoreableException::class);

test('an archived assessment rejects new scores', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $assessment = makeAssessment($this->schoolId, $this->context);
    $this->service->archiveAssessment($assessment);

    recordAssessmentScore($this->schoolId, $assessment, $enrollment, score: 14.0);
})->throws(AssessmentNotScoreableException::class);

test('a teacher can only grade subjects they are assigned to', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $otherSubject = \App\Domains\Academic\Infrastructure\Models\AcademicSubject::create([
        'school_id' => $this->schoolId,
        'name' => 'Histoire',
        'code' => 'HIS',
        'abbreviation' => 'HIS',
        'status' => 'active',
    ]);

    $teacher = makeTeacher($this->schoolId, [$otherSubject->id], 'Marie', 'Curie');

    $user = User::factory()->create(['school_id' => $this->schoolId]);
    $teacher->update(['user_id' => $user->id]);

    $assessment = makeAssessment($this->schoolId, $this->context, ['staff_id' => $teacher->id]);

    app(AssessmentService::class)->recordScore(
        \App\Domains\Assessment\Application\DTOs\RecordScoreData::fromArray([
            'school_id' => $this->schoolId,
            'assessment_id' => $assessment->id,
            'enrollment_id' => $enrollment->id,
            'score' => 14.0,
        ]),
        $user,
    );
})->throws(TeacherNotAuthorizedException::class);

test('a teacher assigned to the subject can record scores', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $user = User::factory()->create(['school_id' => $this->schoolId]);
    $this->context['teacher']->update(['user_id' => $user->id]);

    $assessment = makeAssessment($this->schoolId, $this->context);

    recordAssessmentScore($this->schoolId, $assessment, $enrollment, score: 14.0, actor: $user);

    expect($assessment->scores()->first()->score)->toBe(14.0);
});

test('bulk recording inserts scores for the whole class in one call', function (): void {
    Event::fake([ScoreRecorded::class]);

    $enrollments = collect(range(1, 3))->map(fn (): \App\Domains\Enrollment\Infrastructure\Models\Enrollment => enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    ));

    $assessment = makeAssessment($this->schoolId, $this->context);

    $result = app(AssessmentService::class)->bulkRecordScores(
        BulkScoresData::fromArray([
            'assessment_id' => $assessment->id,
            'items' => $enrollments->map(
                fn ($enrollment): array => ['enrollment_id' => $enrollment->id, 'score' => 15.0],
            )->all(),
        ]),
    );

    expect($result)->toBe(['inserted' => 3, 'skipped' => 0, 'rejected' => 0])
        ->and($assessment->scores()->count())->toBe(3);

    Event::assertDispatched(ScoreRecorded::class, 3);
});

test('bulk recording skips duplicates and rejects out-of-roster and out-of-range rows', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $assessment = makeAssessment($this->schoolId, $this->context);

    $result = app(AssessmentService::class)->bulkRecordScores(
        new BulkScoresData(
            assessmentId: $assessment->id,
            items: [
                new BulkScoreItem(enrollmentId: $enrollment->id, score: 14.0),
                new BulkScoreItem(enrollmentId: $enrollment->id, score: 16.0),
                new BulkScoreItem(enrollmentId: 999999, score: 14.0),
                new BulkScoreItem(enrollmentId: $enrollment->id, score: 30.0),
            ],
        ),
    );

    expect($result)->toBe(['inserted' => 1, 'skipped' => 1, 'rejected' => 2]);
});

test('validation recomputes the validation status of recorded scores', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $assessment = makeAssessment($this->schoolId, $this->context);

    recordAssessmentScore($this->schoolId, $assessment, $enrollment, score: 14.0);

    $assessment->scores()->first()->update(['validation_status' => ScoreValidationStatus::BelowZero]);

    $result = app(AssessmentService::class)->validateScores($assessment);

    expect($result)->toBe(['validated' => 1, 'invalid' => 0])
        ->and($assessment->scores()->first()->refresh()->validation_status)->toBe(ScoreValidationStatus::Valid);
});

test('validation flags an invalid stored score', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $assessment = makeAssessment($this->schoolId, $this->context);

    recordAssessmentScore($this->schoolId, $assessment, $enrollment, score: 14.0);
    $assessment->scores()->first()->update(['score' => 25.0]);

    $result = app(AssessmentService::class)->validateScores($assessment);

    expect($result)->toBe(['validated' => 0, 'invalid' => 1]);
});

test('statistics persist official aggregates for an assessment', function (): void {
    $enrollments = collect(range(1, 3))->map(fn (): \App\Domains\Enrollment\Infrastructure\Models\Enrollment => enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    ));

    $assessment = publishedAssessment($this->schoolId, $this->context);

    recordAssessmentScore($this->schoolId, $assessment, $enrollments[0], score: 10.0);
    recordAssessmentScore($this->schoolId, $assessment, $enrollments[1], score: 16.0);
    recordAssessmentScore($this->schoolId, $assessment, $enrollments[2], score: 4.0);

    $result = app(AssessmentService::class)->generateStatistics($assessment);

    expect($result->totalScores)->toBe(3)
        ->and($result->average)->toBe(10.0)
        ->and($result->minimum)->toBe(4.0)
        ->and($result->maximum)->toBe(16.0)
        ->and($result->passRate)->toBe(66.67)
        ->and($result->completionRate)->toBe(100.0);

    expect($assessment->statistics()->count())->toBe(1)
        ->and(AssessmentStatistics::query()->where('school_id', $this->schoolId)->count())->toBe(1);
});

test('statistics include below-passing scores but never out-of-range rows', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $assessment = publishedAssessment($this->schoolId, $this->context);

    recordAssessmentScore($this->schoolId, $assessment, $enrollment, score: 5.0);

    AssessmentScore::create([
        'school_id' => $this->schoolId,
        'assessment_id' => $assessment->id,
        'enrollment_id' => $enrollment->id,
        'score' => 25.0,
        'percentage' => 125.0,
        'status' => ScoreStatus::Draft,
        'validation_status' => ScoreValidationStatus::AboveMaximum,
        'graded_at' => now(),
    ]);

    $result = app(AssessmentService::class)->generateStatistics($assessment);

    expect($result->totalScores)->toBe(1)
        ->and($result->minimum)->toBe(5.0)
        ->and($result->maximum)->toBe(5.0);
});

test('a published assessment auto-completes when the whole class is scored', function (): void {
    Event::fake([AssessmentCompleted::class]);

    $enrollments = collect(range(1, 2))->map(fn (): \App\Domains\Enrollment\Infrastructure\Models\Enrollment => enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    ));

    $assessment = publishedAssessment($this->schoolId, $this->context);

    recordAssessmentScore($this->schoolId, $assessment, $enrollments[0], score: 12.0);

    expect($assessment->refresh()->status)->toBe(AssessmentStatus::Published);

    recordAssessmentScore($this->schoolId, $assessment, $enrollments[1], score: 14.0);

    expect($assessment->refresh()->status)->toBe(AssessmentStatus::Completed)
        ->and($assessment->completed_at)->not->toBeNull();

    Event::assertDispatched(AssessmentCompleted::class);
});

test('a draft assessment never auto-completes', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $assessment = makeAssessment($this->schoolId, $this->context);

    recordAssessmentScore($this->schoolId, $assessment, $enrollment, score: 12.0);

    expect($assessment->refresh()->status)->toBe(AssessmentStatus::Draft);
});

test('scores can target a specific component with its own maximum', function (): void {
    $enrollment = enrollStudent(
        $this->schoolId,
        $this->context['year']->id,
        $this->context['program']->id,
        $this->context['level']->id,
        $this->context['class']->id,
    );

    $assessment = makeAssessment($this->schoolId, $this->context);

    $component = AssessmentComponent::create([
        'school_id' => $this->schoolId,
        'assessment_id' => $assessment->id,
        'name' => 'Partie écrite',
        'maximum_score' => 10.0,
        'weight' => 1.0,
        'sort_order' => 1,
    ]);

    recordAssessmentScore($this->schoolId, $assessment, $enrollment, $component, score: 5.0);

    expect($assessment->scores()->first()->assessment_component_id)->toBe($component->id)
        ->and($assessment->scores()->first()->percentage)->toBe(50.0);
});
