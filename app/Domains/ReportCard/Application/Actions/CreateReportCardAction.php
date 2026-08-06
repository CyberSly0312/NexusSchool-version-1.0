<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Application\Actions;

use App\Domains\ReportCard\Application\DTOs\CreateReportCardData;
use App\Domains\ReportCard\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;
use App\Domains\ReportCard\Domain\Events\ReportCardCreated;
use App\Domains\ReportCard\Domain\Exceptions\ReportCardException;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Creates a draft report card run (SPR-011). The academic frame is validated
 * through the domain contract and the scope uniqueness is enforced: one run per
 * year + term + class (+ optional section).
 */
final class CreateReportCardAction
{
    public function __construct(
        private readonly AcademicFrameworkContract $academic,
    ) {
    }

    public function execute(CreateReportCardData $data, ?User $actor = null): ReportCard
    {
        return DB::transaction(function () use ($data, $actor): ReportCard {
            $this->assertAcademicContext($data);
            $this->assertUniqueScope($data);

            $reportCard = ReportCard::create([
                'school_id' => $data->schoolId,
                'academic_calendar_year_id' => $data->yearId,
                'academic_calendar_term_id' => $data->termId,
                'school_class_id' => $data->classId,
                'class_section_id' => $data->sectionId,
                'report_card_type' => $data->reportCardType,
                'status' => ReportCardStatus::Draft,
                'created_by' => $actor?->getKey(),
            ]);

            ReportCardCreated::dispatch($reportCard, $actor);

            return $reportCard;
        });
    }

    private function assertAcademicContext(CreateReportCardData $data): void
    {
        if ($this->academic->period($data->schoolId, $data->yearId, $data->termId, $data->classId, $data->sectionId) === null) {
            throw new ReportCardException("Academic frame [year {$data->yearId}, term {$data->termId}] not found.");
        }
    }

    private function assertUniqueScope(CreateReportCardData $data): void
    {
        $exists = ReportCard::query()
            ->where('school_id', $data->schoolId)
            ->where('academic_calendar_year_id', $data->yearId)
            ->where('academic_calendar_term_id', $data->termId)
            ->where('school_class_id', $data->classId)
            ->where('class_section_id', $data->sectionId)
            ->where('status', '!=', ReportCardStatus::Archived->value)
            ->exists();

        if ($exists) {
            throw new ReportCardException('A report card run already exists for this period, class and section.');
        }
    }
}
