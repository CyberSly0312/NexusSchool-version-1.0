<?php

declare(strict_types=1);

use App\Domains\Assessment\Application\AssessmentService;
use App\Domains\Assessment\Application\DTOs\UpdateAssessmentData;
use App\Domains\Assessment\Domain\Enums\AssessmentStatus;
use App\Domains\Assessment\Domain\Events\AssessmentArchived;
use App\Domains\Assessment\Domain\Events\AssessmentCreated;
use App\Domains\Assessment\Domain\Events\AssessmentPublished;
use App\Domains\Assessment\Domain\Exceptions\AssessmentDefinitionImmutableException;
use App\Domains\Assessment\Domain\Exceptions\AssessmentException;
use App\Domains\Assessment\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domains\Assessment\Domain\Exceptions\WeightTotalExceededException;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makeAssessmentContext($this->schoolId);
    $this->service = app(AssessmentService::class);
});

function assessmentUpdateData(array $overrides = []): UpdateAssessmentData
{
    return UpdateAssessmentData::fromArray(array_merge([
        'assessment_id' => 1,
        'title' => 'Devoir de contrôle mis à jour',
        'maximum_score' => 20.0,
        'passing_score' => 10.0,
        'weight' => 1.0,
    ], $overrides));
}

test('creation writes a draft assessment bound to the academic context', function (): void {
    Event::fake([AssessmentCreated::class]);

    $assessment = makeAssessment($this->schoolId, $this->context);

    expect($assessment)->toBeInstanceOf(Assessment::class)
        ->and($assessment->school_id)->toBe($this->schoolId)
        ->and($assessment->status)->toBe(AssessmentStatus::Draft)
        ->and($assessment->academic_calendar_year_id)->toBe($this->context['year']->id)
        ->and($assessment->academic_calendar_term_id)->toBe($this->context['term']->id)
        ->and($assessment->academic_subject_id)->toBe($this->context['subject']->id)
        ->and($assessment->school_class_id)->toBe($this->context['class']->id)
        ->and($assessment->staff_id)->toBe($this->context['teacher']->id)
        ->and($assessment->assessment_type_id)->toBe($this->context['type']->id)
        ->and($assessment->maximum_score)->toBe(20.0)
        ->and($assessment->passing_score)->toBe(10.0)
        ->and($assessment->weight)->toBe(1.0)
        ->and($assessment->uuid)->not->toBeNull();

    Event::assertDispatched(AssessmentCreated::class);
});

test('creation rejects an unknown academic year', function (): void {
    makeAssessment($this->schoolId, $this->context, ['academic_calendar_year_id' => 999999]);
})->throws(AssessmentException::class);

test('creation rejects an unknown subject', function (): void {
    makeAssessment($this->schoolId, $this->context, ['academic_subject_id' => 999999]);
})->throws(AssessmentException::class);

test('creation rejects a non-teacher staff reference', function (): void {
    makeAssessment($this->schoolId, $this->context, ['staff_id' => 999999]);
})->throws(AssessmentException::class);

test('a draft assessment definition can be updated', function (): void {
    $assessment = makeAssessment($this->schoolId, $this->context);

    $updated = $this->service->updateAssessment(
        $assessment,
        assessmentUpdateData([
            'assessment_id' => $assessment->id,
            'title' => 'Devoir révisé',
            'maximum_score' => 20.0,
            'passing_score' => 12.0,
        ]),
    );

    expect($updated->title)->toBe('Devoir révisé')
        ->and($updated->passing_score)->toBe(12.0)
        ->and($updated->academic_calendar_year_id)->toBe($this->context['year']->id);
});

test('a draft can be published and becomes definition-immutable', function (): void {
    Event::fake([AssessmentPublished::class]);

    $assessment = makeAssessment($this->schoolId, $this->context);

    $published = $this->service->publishAssessment($assessment);

    expect($published->status)->toBe(AssessmentStatus::Published)
        ->and($published->published_at)->not->toBeNull();

    $this->service->updateAssessment(
        $published,
        assessmentUpdateData(['assessment_id' => $published->id, 'title' => 'Trop tard']),
    );
})->throws(AssessmentDefinitionImmutableException::class);

test('a published assessment cannot be published again', function (): void {
    $assessment = makeAssessment($this->schoolId, $this->context);
    $this->service->publishAssessment($assessment);

    $this->service->publishAssessment($assessment->refresh());
})->throws(InvalidStatusTransitionException::class);

test('an assessment can be archived and then rejects scores', function (): void {
    Event::fake([AssessmentArchived::class]);

    $assessment = makeAssessment($this->schoolId, $this->context);

    $archived = $this->service->archiveAssessment($assessment);

    expect($archived->status)->toBe(AssessmentStatus::Archived)
        ->and($archived->archived_at)->not->toBeNull();

    Event::assertDispatched(AssessmentArchived::class);
});

test('the weight ceiling is enforced across assessments of the same subject and term', function (): void {
    $first = makeAssessment($this->schoolId, $this->context, ['weight' => 99.0]);
    expect($first->weight)->toBe(99.0);

    makeAssessment($this->schoolId, $this->context, ['weight' => 2.0]);
})->throws(WeightTotalExceededException::class);

test('the weight ceiling ignores archived assessments', function (): void {
    $first = makeAssessment($this->schoolId, $this->context, ['weight' => 99.0]);
    $this->service->archiveAssessment($first);

    $second = makeAssessment($this->schoolId, $this->context, ['weight' => 2.0]);

    expect($second->weight)->toBe(2.0);
});

test('an archived assessment remains definition-immutable', function (): void {
    $assessment = makeAssessment($this->schoolId, $this->context);
    $this->service->archiveAssessment($assessment);

    $this->service->updateAssessment(
        $assessment->refresh(),
        assessmentUpdateData(['assessment_id' => $assessment->id]),
    );
})->throws(AssessmentDefinitionImmutableException::class);
