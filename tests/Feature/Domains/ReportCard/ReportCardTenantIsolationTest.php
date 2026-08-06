<?php

declare(strict_types=1);

use App\Domains\ReportCard\Application\DTOs\CreateReportCardData;
use App\Domains\ReportCard\Application\ReportCardService;
use App\Domains\ReportCard\Domain\Enums\ReportCardType;
use App\Domains\ReportCard\Domain\Exceptions\ReportCardException;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Domains\ReportCard\Infrastructure\Models\ReportCardEntry;

beforeEach(function (): void {
    $this->service = app(ReportCardService::class);
});

function reportCardScopeData(string $schoolId, array $context): CreateReportCardData
{
    return CreateReportCardData::fromArray([
        'school_id' => $schoolId,
        'academic_calendar_year_id' => $context['year']->id,
        'academic_calendar_term_id' => $context['term']->id,
        'school_class_id' => $context['class']->id,
        'class_section_id' => null,
        'report_card_type' => ReportCardType::Term->value,
    ]);
}

test('report card runs do not leak across tenants', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextA = makeAssessmentContext($schoolA, false);
    $reportCardA = $this->service->createReportCard(reportCardScopeData($schoolA, $contextA));

    expect(ReportCard::query()->forSchool($schoolA)->count())->toBe(1)
        ->and(ReportCard::query()->forSchool($schoolB)->count())->toBe(0)
        ->and(ReportCard::query()->count())->toBe(1)
        ->and($reportCardA->school_id)->toBe($schoolA);
});

test('the scope uniqueness is enforced per tenant', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextA = makeAssessmentContext($schoolA, false);
    $contextB = makeAssessmentContext($schoolB, false);

    $this->service->createReportCard(reportCardScopeData($schoolA, $contextA));

    expect(fn () => $this->service->createReportCard(reportCardScopeData($schoolA, $contextA)))
        ->toThrow(ReportCardException::class)
        ->and($this->service->createReportCard(reportCardScopeData($schoolB, $contextB)))->toBeInstanceOf(ReportCard::class);
});

test('a run refuses an academic frame owned by another tenant', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextA = makeAssessmentContext($schoolA, false);

    expect(fn () => $this->service->createReportCard(CreateReportCardData::fromArray([
        'school_id' => $schoolB,
        'academic_calendar_year_id' => $contextA['year']->id,
        'academic_calendar_term_id' => $contextA['term']->id,
        'school_class_id' => $contextA['class']->id,
        'report_card_type' => ReportCardType::Term->value,
    ])))->toThrow(ReportCardException::class);
});

test('report card entries are scoped to their run school', function (): void {
    $schoolA = makeTenant();
    $schoolB = makeTenant();

    $contextA = makeAssessmentContext($schoolA, false);
    $contextB = makeAssessmentContext($schoolB, false);

    $reportCardA = $this->service->createReportCard(reportCardScopeData($schoolA, $contextA));
    ReportCardEntry::factory()
        ->forSchool($schoolA)
        ->forReportCard($reportCardA->id)
        ->create(['student_id' => 1]);

    expect(ReportCardEntry::query()->forSchool($schoolA)->count())->toBe(1)
        ->and(ReportCardEntry::query()->forSchool($schoolB)->count())->toBe(0);
});
