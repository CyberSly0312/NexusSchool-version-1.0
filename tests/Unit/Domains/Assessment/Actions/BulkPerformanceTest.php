<?php

declare(strict_types=1);

use App\Domains\Assessment\Application\AssessmentService;
use App\Domains\Assessment\Application\DTOs\BulkScoreItem;
use App\Domains\Assessment\Application\DTOs\BulkScoresData;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makeAssessmentContext($this->schoolId);
});

test('bulk recording a large class stays bounded in query count', function (): void {
    $enrollments = collect(range(1, 250))->map(
        fn (): \App\Domains\Enrollment\Infrastructure\Models\Enrollment => enrollStudent(
            $this->schoolId,
            $this->context['year']->id,
            $this->context['program']->id,
            $this->context['level']->id,
            $this->context['class']->id,
        ),
    );

    $assessment = makeAssessment($this->schoolId, $this->context);

    $items = $enrollments->map(
        fn ($enrollment): BulkScoreItem => new BulkScoreItem(enrollmentId: $enrollment->id, score: 14.0),
    )->all();

    DB::flushQueryLog();
    DB::enableQueryLog();

    $result = app(AssessmentService::class)->bulkRecordScores(
        new BulkScoresData(assessmentId: $assessment->id, items: $items),
    );

    $queryCount = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($result)->toBe(['inserted' => 250, 'skipped' => 0, 'rejected' => 0])
        ->and($assessment->scores()->count())->toBe(250)
        ->and($queryCount)->toBeLessThan(30);
});
