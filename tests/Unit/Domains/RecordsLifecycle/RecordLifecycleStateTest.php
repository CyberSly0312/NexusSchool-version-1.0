<?php

declare(strict_types=1);

use App\Domains\RecordsLifecycle\Domain\Enums\RecordLifecycleState;
use App\Domains\RecordsLifecycle\Infrastructure\Models\RecordLifecycle;

test('it allows the approved lifecycle transitions for the envelope', function (): void {
    $record = new RecordLifecycle([
        'school_id' => 'school-a',
        'source_domain' => 'report_card',
        'source_type' => 'report_card',
        'source_id' => 42,
        'lifecycle_state' => RecordLifecycleState::Registered,
    ]);

    expect($record->canTransitionTo(RecordLifecycleState::Published))->toBeTrue()
        ->and($record->canTransitionTo(RecordLifecycleState::Archived))->toBeTrue();
});

test('it rejects transitions that bypass the envelope rules', function (): void {
    $record = new RecordLifecycle([
        'school_id' => 'school-a',
        'source_domain' => 'report_card',
        'source_type' => 'report_card',
        'source_id' => 42,
        'lifecycle_state' => RecordLifecycleState::Published,
    ]);

    expect($record->canTransitionTo(RecordLifecycleState::Registered))->toBeFalse()
        ->and($record->canTransitionTo(RecordLifecycleState::Published))->toBeFalse();
});

test('it allows published to archived transition', function (): void {
    $record = new RecordLifecycle([
        'school_id' => 'school-a',
        'source_domain' => 'report_card',
        'source_type' => 'report_card',
        'source_id' => 42,
        'lifecycle_state' => RecordLifecycleState::Published,
    ]);

    expect($record->canTransitionTo(RecordLifecycleState::Archived))->toBeTrue();
});

test('it rejects all transitions from archived state', function (): void {
    $record = new RecordLifecycle([
        'school_id' => 'school-a',
        'source_domain' => 'report_card',
        'source_type' => 'report_card',
        'source_id' => 42,
        'lifecycle_state' => RecordLifecycleState::Archived,
    ]);

    expect($record->canTransitionTo(RecordLifecycleState::Registered))->toBeFalse()
        ->and($record->canTransitionTo(RecordLifecycleState::Published))->toBeFalse()
        ->and($record->canTransitionTo(RecordLifecycleState::Archived))->toBeFalse();
});

test('it rejects self-transition from registered state', function (): void {
    $record = new RecordLifecycle([
        'school_id' => 'school-a',
        'source_domain' => 'report_card',
        'source_type' => 'report_card',
        'source_id' => 42,
        'lifecycle_state' => RecordLifecycleState::Registered,
    ]);

    expect($record->canTransitionTo(RecordLifecycleState::Registered))->toBeFalse();
});
