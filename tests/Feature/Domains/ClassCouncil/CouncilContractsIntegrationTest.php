<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\AcademicCommunication\Domain\Contracts\ClassCouncilContract as MinutesClassCouncilContract;
use App\Domains\AcademicCommunication\Domain\References\CouncilDecisionReference as MinutesDecisionReference;
use App\Domains\ClassCouncil\Application\ClassCouncilService;
use App\Domains\ClassCouncil\Application\DTOs\CreateCouncilSessionData;
use App\Domains\ClassCouncil\Application\DTOs\RecordCouncilDecisionData;
use App\Domains\ClassCouncil\Application\DTOs\RecordCouncilDeliberationData;
use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionCategory;
use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType;
use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Domains\ReportCard\Domain\Contracts\ClassCouncilContract as BulletinClassCouncilContract;
use App\Domains\ReportCard\Domain\References\CouncilDecisionReference as BulletinDecisionReference;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makeAssessmentContext($this->schoolId, false);

    $this->level = EducationLevel::firstOrCreate(
        ['school_id' => $this->schoolId, 'name' => 'Collège'],
        [
            'code' => 'ELC',
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

    $this->service = app(ClassCouncilService::class);
});

function classCouncilPublishedSession(array $context, object $enrollment, string $schoolId): CouncilSession
{
    $service = app(ClassCouncilService::class);

    $session = $service->createSession(CreateCouncilSessionData::fromArray([
        'school_id' => $schoolId,
        'academic_calendar_year_id' => $context['year']->id,
        'academic_calendar_term_id' => $context['term']->id,
        'school_class_id' => $context['class']->id,
    ]));

    $service->conveneSession($session);

    $service->recordDecision(RecordCouncilDecisionData::fromArray([
        'school_id' => $schoolId,
        'council_session_id' => $session->id,
        'student_id' => $enrollment->student_id,
        'decision_type' => CouncilDecisionType::Passed->value,
        'category' => CouncilDecisionCategory::Promotion->value,
        'label' => CouncilDecisionType::Passed->label(),
        'summary' => 'Résultats conformes',
        'recommendation' => 'Passage en classe supérieure',
        'decision_date' => '2026-07-01',
    ]));

    return $service->publishSession($session);
}

test('a convened session exposes no decision to the bulletin contract', function (): void {
    $session = $this->service->createSession(CreateCouncilSessionData::fromArray([
        'school_id' => $this->schoolId,
        'academic_calendar_year_id' => $this->context['year']->id,
        'academic_calendar_term_id' => $this->context['term']->id,
        'school_class_id' => $this->context['class']->id,
    ]));
    $this->service->conveneSession($session);

    $contract = app(BulletinClassCouncilContract::class);

    expect($contract->decisionsForStudent(
        $this->schoolId,
        $this->enrollment->student_id,
        $this->context['year']->id,
        $this->context['term']->id,
    ))->toBe([]);
});

test('the bulletin contract exposes published council decisions', function (): void {
    $session = classCouncilPublishedSession($this->context, $this->enrollment, $this->schoolId);

    $contract = app(BulletinClassCouncilContract::class);

    $decisions = $contract->decisionsForStudent(
        $this->schoolId,
        $this->enrollment->student_id,
        $this->context['year']->id,
        $this->context['term']->id,
    );

    expect($decisions)->toHaveCount(1)
        ->and($decisions[0])->toBeInstanceOf(BulletinDecisionReference::class)
        ->and($decisions[0]->code)->toBe(sprintf('%s-001', $session->reference))
        ->and($decisions[0]->label)->toBe(CouncilDecisionType::Passed->label())
        ->and($decisions[0]->decision)->toBe('passed');
});

test('the minutes contract exposes published decisions of a session', function (): void {
    $session = classCouncilPublishedSession($this->context, $this->enrollment, $this->schoolId);

    $contract = app(MinutesClassCouncilContract::class);

    $decisions = $contract->publishedDecisions($this->schoolId, $session->id);

    expect($decisions)->toHaveCount(1)
        ->and($decisions[0])->toBeInstanceOf(MinutesDecisionReference::class)
        ->and($decisions[0]->id)->toBe($decisions[0]->id)
        ->and($decisions[0]->summary)->toBe('Résultats conformes')
        ->and($decisions[0]->category)->toBe('promotion')
        ->and($decisions[0]->studentId)->toBe($this->enrollment->student_id)
        ->and($decisions[0]->decisionDate)->toBe('2026-07-01')
        ->and($decisions[0]->recommendation)->toBe('Passage en classe supérieure');
});

test('confidential deliberations never reach the minutes contract', function (): void {
    $session = $this->service->createSession(CreateCouncilSessionData::fromArray([
        'school_id' => $this->schoolId,
        'academic_calendar_year_id' => $this->context['year']->id,
        'academic_calendar_term_id' => $this->context['term']->id,
        'school_class_id' => $this->context['class']->id,
    ]));
    $this->service->conveneSession($session);

    $this->service->recordDecision(RecordCouncilDecisionData::fromArray([
        'school_id' => $this->schoolId,
        'council_session_id' => $session->id,
        'student_id' => $this->enrollment->student_id,
        'decision_type' => CouncilDecisionType::Passed->value,
        'category' => CouncilDecisionCategory::Promotion->value,
        'label' => CouncilDecisionType::Passed->label(),
        'summary' => 'Résultats conformes',
    ]));

    $this->service->recordDeliberation(RecordCouncilDeliberationData::fromArray([
        'school_id' => $this->schoolId,
        'council_session_id' => $session->id,
        'student_id' => $this->enrollment->student_id,
        'topic' => 'Dossier élève',
        'summary' => 'Échanges confidentiels sur le dossier.',
    ]));

    $this->service->publishSession($session);

    $contract = app(MinutesClassCouncilContract::class);

    $decisions = $contract->publishedDecisions($this->schoolId, $session->id);

    expect($decisions)->toHaveCount(1)
        ->and($decisions[0]->summary)->toBe('Résultats conformes')
        ->and($session->deliberations()->count())->toBe(1);
});

test('archived sessions keep exposing their decisions', function (): void {
    $session = classCouncilPublishedSession($this->context, $this->enrollment, $this->schoolId);
    $this->service->archiveSession($session);

    expect($session->refresh()->status)->toBe(CouncilSessionStatus::Archived);

    $bulletin = app(BulletinClassCouncilContract::class);
    $minutes = app(MinutesClassCouncilContract::class);

    expect($bulletin->decisionsForStudent(
        $this->schoolId,
        $this->enrollment->student_id,
        $this->context['year']->id,
        $this->context['term']->id,
    ))->toHaveCount(1)
        ->and($minutes->publishedDecisions($this->schoolId, $session->id))->toHaveCount(1);
});
