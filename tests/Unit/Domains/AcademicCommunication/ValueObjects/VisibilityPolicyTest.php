<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;
use App\Domains\AcademicCommunication\Domain\ValueObjects\VisibilityPolicy;

/**
 * The visibility policy drives the renderer's redaction. It maps every scope to
 * the document sections it may see and flags self-scoped recipients (parents,
 * students) who must never receive another person's data.
 */
test('wildcard scope sees every section', function (): void {
    $policy = VisibilityPolicy::fromArray([
        VisibilityScope::Director->value => ['sections' => ['*']],
    ]);

    expect($policy->canSeeSection(VisibilityScope::Director, 'results'))->toBeTrue()
        ->and($policy->canSeeSection(VisibilityScope::Director, 'student'))->toBeTrue();
});

test('explicit sections restrict visibility', function (): void {
    $policy = VisibilityPolicy::fromArray([
        VisibilityScope::Parent->value => ['sections' => ['student', 'results']],
    ]);

    expect($policy->canSeeSection(VisibilityScope::Parent, 'student'))->toBeTrue()
        ->and($policy->canSeeSection(VisibilityScope::Parent, 'results'))->toBeTrue()
        ->and($policy->canSeeSection(VisibilityScope::Parent, 'decisions'))->toBeFalse();
});

test('unknown scope defaults to the configured sections only', function (): void {
    $policy = VisibilityPolicy::fromArray([
        VisibilityScope::Teacher->value => ['sections' => ['teaching']],
    ]);

    expect($policy->sectionsFor(VisibilityScope::Teacher))->toBe(['teaching'])
        ->and($policy->sectionsFor(VisibilityScope::Secretary))->toBe([]);
});

test('self-scoped scopes default to self_only', function (): void {
    $policy = VisibilityPolicy::fromArray([]);

    expect($policy->isSelfOnly(VisibilityScope::Parent))->toBeTrue()
        ->and($policy->isSelfOnly(VisibilityScope::Student))->toBeTrue()
        ->and($policy->isSelfOnly(VisibilityScope::Director))->toBeFalse();
});

test('fromArray rejects an unknown scope key', function (): void {
    VisibilityPolicy::fromArray(['unknown' => ['sections' => ['*']]]);
})->throws(InvalidArgumentException::class);

test('fromArray rejects a scope without a section list', function (): void {
    VisibilityPolicy::fromArray([
        VisibilityScope::Director->value => ['self_only' => true],
    ]);
})->throws(InvalidArgumentException::class);
