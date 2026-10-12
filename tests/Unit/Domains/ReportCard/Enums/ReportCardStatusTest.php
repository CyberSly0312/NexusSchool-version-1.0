<?php

declare(strict_types=1);

use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;
use App\Domains\ReportCard\Domain\Enums\ReportCardType;
use App\Domains\ReportCard\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;

test('a report card run is created as a draft', function (): void {
    $schoolId = makeTenant();
    $context = makeAssessmentContext($schoolId, false);

    $reportCard = ReportCard::factory()
        ->forSchool($schoolId)
        ->forClass($context['class']->id)
        ->create([
            'academic_calendar_year_id' => $context['year']->id,
            'academic_calendar_term_id' => $context['term']->id,
        ]);

    expect($reportCard->status)->toBe(ReportCardStatus::Draft)
        ->and($reportCard->isDraft())->toBeTrue()
        ->and($reportCard->isImmutable())->toBeFalse();
});

test('the lifecycle is forward only', function (): void {
    expect(ReportCardStatus::Generated->allowsTransitionFrom(ReportCardStatus::Draft))->toBeTrue()
        ->and(ReportCardStatus::Generated->allowsTransitionFrom(ReportCardStatus::Generated))->toBeTrue()
        ->and(ReportCardStatus::Generated->allowsTransitionFrom(ReportCardStatus::Published))->toBeTrue()
        ->and(ReportCardStatus::Published->allowsTransitionFrom(ReportCardStatus::Generated))->toBeTrue()
        ->and(ReportCardStatus::Archived->allowsTransitionFrom(ReportCardStatus::Generated))->toBeTrue()
        ->and(ReportCardStatus::Archived->allowsTransitionFrom(ReportCardStatus::Published))->toBeTrue()
        ->and(ReportCardStatus::Archived->allowsTransitionFrom(ReportCardStatus::Archived))->toBeFalse();
});

test('no transition leads back to draft', function (): void {
    foreach (ReportCardStatus::cases() as $from) {
        expect(ReportCardStatus::Draft->allowsTransitionFrom($from))->toBeFalse();
    }
});

test('publishing requires a generated run', function (): void {
    foreach (ReportCardStatus::cases() as $from) {
        if ($from === ReportCardStatus::Generated) {
            continue;
        }

        expect(ReportCardStatus::Published->allowsTransitionFrom($from))->toBeFalse();
    }
});

test('a run cannot transition to archived from draft', function (): void {
    expect(ReportCardStatus::Archived->allowsTransitionFrom(ReportCardStatus::Draft))->toBeFalse();
});

test('a run is regenerable only while generated or published', function (): void {
    expect(ReportCardStatus::Draft->isRegenerable())->toBeFalse()
        ->and(ReportCardStatus::Generated->isRegenerable())->toBeTrue()
        ->and(ReportCardStatus::Published->isRegenerable())->toBeTrue()
        ->and(ReportCardStatus::Archived->isRegenerable())->toBeFalse();
});

test('published and archived runs are immutable', function (): void {
    expect(ReportCardStatus::Published->isImmutable())->toBeTrue()
        ->and(ReportCardStatus::Archived->isImmutable())->toBeTrue()
        ->and(ReportCardStatus::Generated->isImmutable())->toBeFalse();
});

test('assertCanTransitionTo throws on an invalid transition', function (): void {
    $schoolId = makeTenant();
    $context = makeAssessmentContext($schoolId, false);

    $reportCard = ReportCard::factory()
        ->forSchool($schoolId)
        ->forClass($context['class']->id)
        ->create([
            'status' => ReportCardStatus::Draft,
            'academic_calendar_year_id' => $context['year']->id,
            'academic_calendar_term_id' => $context['term']->id,
        ]);

    expect(fn () => $reportCard->assertCanTransitionTo(ReportCardStatus::Published))
        ->toThrow(InvalidStatusTransitionException::class);
});

test('the term and annual types are exposed', function (): void {
    expect(ReportCardType::Term->value)->toBe('term')
        ->and(ReportCardType::Annual->value)->toBe('annual');
});
