<?php

declare(strict_types=1);

use App\Domains\ClassCouncil\Application\ClassCouncilService;
use App\Domains\ClassCouncil\Application\DTOs\CreateCouncilSessionData;
use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\RecordsLifecycle\Domain\Enums\RecordLifecycleState;
use App\Domains\RecordsLifecycle\Infrastructure\Models\RecordLifecycle;

function ccLifecycleData(string $schoolId, array $context, ?int $sectionId = null): CreateCouncilSessionData
{
    return CreateCouncilSessionData::fromArray([
        'school_id' => $schoolId,
        'academic_calendar_year_id' => $context['year']->id,
        'academic_calendar_term_id' => $context['term']->id,
        'school_class_id' => $context['class']->id,
        'class_section_id' => $sectionId,
    ]);
}

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->context = makeAssessmentContext($this->schoolId, false);

    $this->level = \App\Domains\Academic\Infrastructure\Models\EducationLevel::firstOrCreate(
        ['school_id' => $this->schoolId, 'name' => 'Collège'],
        [
            'code' => 'EL'.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(3)),
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

test('session creation registers a lifecycle envelope with correct provenance', function (): void {
    $session = $this->service->createSession(ccLifecycleData($this->schoolId, $this->context));

    $lifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'class_council')
        ->where('source_type', 'council_session')
        ->where('source_id', (string) $session->getKey())
        ->first();

    expect($lifecycle)->not->toBeNull()
        ->and($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Registered)
        ->and($lifecycle->school_id)->toBe($this->schoolId)
        ->and($lifecycle->source_version)->toBe($session->uuid)
        ->and($lifecycle->origin_event)->toBe('CouncilSessionCreated')
        ->and($lifecycle->academic_context)->toBe([
            'year_id' => $session->academic_calendar_year_id,
            'term_id' => $session->academic_calendar_term_id,
            'class_id' => $session->school_class_id,
            'section_id' => $session->class_section_id,
        ]);
});

test('session publication transitions the lifecycle envelope to published', function (): void {
    $session = $this->service->createSession(ccLifecycleData($this->schoolId, $this->context));
    $this->service->conveneSession($session);
    $this->service->publishSession($session);

    $lifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'class_council')
        ->where('source_type', 'council_session')
        ->where('source_id', (string) $session->getKey())
        ->first();

    expect($lifecycle)->not->toBeNull()
        ->and($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Published)
        ->and($lifecycle->published_at)->not->toBeNull();
});

test('session archival transitions the lifecycle envelope to archived', function (): void {
    $session = $this->service->createSession(ccLifecycleData($this->schoolId, $this->context));
    $this->service->conveneSession($session);
    $this->service->publishSession($session);
    $this->service->archiveSession($session);

    $lifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'class_council')
        ->where('source_type', 'council_session')
        ->where('source_id', (string) $session->getKey())
        ->first();

    expect($lifecycle)->not->toBeNull()
        ->and($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Archived)
        ->and($lifecycle->archived_at)->not->toBeNull();
});

test('session publish-then-archive transitions lifecycle through published to archived', function (): void {
    $session = $this->service->createSession(ccLifecycleData($this->schoolId, $this->context));
    $this->service->conveneSession($session);
    $this->service->publishSession($session);
    $this->service->archiveSession($session);

    $lifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'class_council')
        ->where('source_type', 'council_session')
        ->where('source_id', (string) $session->getKey())
        ->first();

    expect($lifecycle->lifecycle_state)->toBe(RecordLifecycleState::Archived)
        ->and($lifecycle->published_at)->not->toBeNull()
        ->and($lifecycle->archived_at)->not->toBeNull();
});

test('lifecycle records from different schools remain isolated', function (): void {
    $otherSchool = makeTenant('Other School');
    $otherContext = makeAssessmentContext($otherSchool, false);

    $otherLevel = \App\Domains\Academic\Infrastructure\Models\EducationLevel::firstOrCreate(
        ['school_id' => $otherSchool, 'name' => 'Collège'],
        [
            'code' => 'EL'.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(3)),
            'sort_order' => 1,
            'status' => 'active',
        ],
    );
    enrollStudent(
        $otherSchool,
        $otherContext['year']->id,
        $otherContext['program']->id,
        $otherLevel->id,
        $otherContext['class']->id,
    );

    $sessionA = $this->service->createSession(ccLifecycleData($this->schoolId, $this->context));
    $sessionB = $this->service->createSession(ccLifecycleData($otherSchool, $otherContext));

    $schoolALifecycle = RecordLifecycle::query()
        ->forSchool($this->schoolId)
        ->where('source_domain', 'class_council')
        ->where('source_type', 'council_session')
        ->first();

    $schoolBLifecycle = RecordLifecycle::query()
        ->forSchool($otherSchool)
        ->where('source_domain', 'class_council')
        ->where('source_type', 'council_session')
        ->first();

    expect($schoolALifecycle)->not->toBeNull()
        ->and($schoolBLifecycle)->not->toBeNull()
        ->and($schoolALifecycle->school_id)->toBe($this->schoolId)
        ->and($schoolBLifecycle->school_id)->toBe($otherSchool)
        ->and($schoolALifecycle->source_id)->not->toBe($schoolBLifecycle->source_id);
});
