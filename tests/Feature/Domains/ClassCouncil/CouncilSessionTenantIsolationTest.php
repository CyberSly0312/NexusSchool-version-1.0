<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\ClassCouncil\Application\ClassCouncilService;
use App\Domains\ClassCouncil\Application\DTOs\CreateCouncilSessionData;
use App\Domains\ClassCouncil\Application\DTOs\RecordCouncilDecisionData;
use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionCategory;
use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType;
use App\Domains\ClassCouncil\Domain\Exceptions\ClassCouncilException;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDecision;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;

beforeEach(function (): void {
    $this->service = app(ClassCouncilService::class);
});

function classCouncilTenantData(string $schoolId, array $context): CreateCouncilSessionData
{
    return CreateCouncilSessionData::fromArray([
        'school_id' => $schoolId,
        'academic_calendar_year_id' => $context['year']->id,
        'academic_calendar_term_id' => $context['term']->id,
        'school_class_id' => $context['class']->id,
    ]);
}

test('council sessions do not leak across tenants', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextA = makeAssessmentContext($schoolA, false);
    $sessionA = $this->service->createSession(classCouncilTenantData($schoolA, $contextA));

    expect(CouncilSession::query()->forSchool($schoolA)->count())->toBe(1)
        ->and(CouncilSession::query()->forSchool($schoolB)->count())->toBe(0)
        ->and(CouncilSession::query()->count())->toBe(1)
        ->and($sessionA->school_id)->toBe($schoolA);
});

test('a session refuses an academic frame owned by another tenant', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextA = makeAssessmentContext($schoolA, false);

    expect(fn () => $this->service->createSession(CreateCouncilSessionData::fromArray([
        'school_id' => $schoolB,
        'academic_calendar_year_id' => $contextA['year']->id,
        'academic_calendar_term_id' => $contextA['term']->id,
        'school_class_id' => $contextA['class']->id,
    ])))->toThrow(ClassCouncilException::class);
});

test('convening reviews only the session school enrollments', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextA = makeAssessmentContext($schoolA, false);
    $contextB = makeAssessmentContext($schoolB, false);

    $levelA = EducationLevel::firstOrCreate(
        ['school_id' => $schoolA, 'name' => 'Collège'],
        [
            'code' => 'ELA',
            'sort_order' => 1,
            'status' => 'active',
        ],
    );
    $enrollmentA = enrollStudent($schoolA, $contextA['year']->id, $contextA['program']->id, $levelA->id, $contextA['class']->id);

    $levelB = EducationLevel::firstOrCreate(
        ['school_id' => $schoolB, 'name' => 'Collège'],
        [
            'code' => 'ELB',
            'sort_order' => 1,
            'status' => 'active',
        ],
    );
    $enrollmentB = enrollStudent($schoolB, $contextB['year']->id, $contextB['program']->id, $levelB->id, $contextB['class']->id);

    $session = $this->service->createSession(classCouncilTenantData($schoolA, $contextA));
    $reviews = $this->service->conveneSession($session);

    expect($reviews)->toHaveCount(1)
        ->and($reviews[0]->student->id)->toBe($enrollmentA->student_id)
        ->and($reviews[0]->student->id)->not->toBe($enrollmentB->student_id);
});

test('decisions are scoped to their session school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextA = makeAssessmentContext($schoolA, false);
    $levelA = EducationLevel::firstOrCreate(
        ['school_id' => $schoolA, 'name' => 'Collège'],
        [
            'code' => 'ELA',
            'sort_order' => 1,
            'status' => 'active',
        ],
    );
    $enrollmentA = enrollStudent($schoolA, $contextA['year']->id, $contextA['program']->id, $levelA->id, $contextA['class']->id);

    $session = $this->service->createSession(classCouncilTenantData($schoolA, $contextA));
    $this->service->conveneSession($session);

    $this->service->recordDecision(RecordCouncilDecisionData::fromArray([
        'school_id' => $schoolA,
        'council_session_id' => $session->id,
        'student_id' => $enrollmentA->student_id,
        'decision_type' => CouncilDecisionType::Passed->value,
        'category' => CouncilDecisionCategory::Promotion->value,
        'label' => CouncilDecisionType::Passed->label(),
    ]));

    expect(CouncilDecision::query()->forSchool($schoolA)->count())->toBe(1)
        ->and(CouncilDecision::query()->forSchool($schoolB)->count())->toBe(0);
});
