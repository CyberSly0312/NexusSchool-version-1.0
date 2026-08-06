<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Application\Actions;

use App\Domains\ReportCard\Application\Services\ReportCardContentComposer;
use App\Domains\ReportCard\Domain\Contracts\AcademicCommunicationContract;
use App\Domains\ReportCard\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\ReportCard\Domain\Contracts\EnrollmentContract;
use App\Domains\ReportCard\Domain\Enums\ReportCardEntryStatus;
use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;
use App\Domains\ReportCard\Domain\Events\ReportCardRegenerated;
use App\Domains\ReportCard\Domain\Exceptions\ReportCardException;
use App\Domains\ReportCard\Domain\References\AcademicDocumentReference;
use App\Domains\ReportCard\Domain\References\AcademicPeriodReference;
use App\Domains\ReportCard\Domain\References\EnrollmentReference;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Domains\ReportCard\Infrastructure\Models\ReportCardEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Re-composes the bulletins of a generated or published run (SPR-011).
 *
 * The audited counterpart of the Gradebook Unlock: a run whose grades or
 * standing changed after generation can be re-composed. Fresh official
 * documents are produced through the Academic Communication contract and the
 * run returns to Generated. The engine never performs any calculation.
 */
final class RegenerateReportCardAction
{
    public function __construct(
        private readonly AcademicFrameworkContract $academic,
        private readonly EnrollmentContract $enrollment,
        private readonly ReportCardContentComposer $composer,
        private readonly AcademicCommunicationContract $communication,
    ) {
    }

    public function execute(ReportCard $reportCard, ?User $actor = null): ReportCard
    {
        return DB::transaction(function () use ($reportCard, $actor): ReportCard {
            if (! $reportCard->status->isRegenerable()) {
                throw new ReportCardException("Report card run [{$reportCard->status->value}] cannot be regenerated.");
            }

            $reportCard->assertCanTransitionTo(ReportCardStatus::Generated);

            $period = $this->requirePeriod($reportCard);
            $enrollments = $this->enrollment->activeEnrollments(
                $reportCard->school_id,
                $reportCard->academic_calendar_year_id,
                $reportCard->school_class_id,
                $reportCard->class_section_id,
            );

            $documentNumbers = [];

            foreach ($enrollments as $enrollment) {
                $document = $this->composeEntry($reportCard, $enrollment, $period, $actor);

                if ($document !== null) {
                    $documentNumbers[] = $document->documentNumber;
                }
            }

            $reportCard->status = ReportCardStatus::Generated;
            $reportCard->generated_at = now();
            $reportCard->save();

            ReportCardRegenerated::dispatch($reportCard, $documentNumbers, $actor);

            return $reportCard->refresh();
        });
    }

    private function requirePeriod(ReportCard $reportCard): AcademicPeriodReference
    {
        $period = $this->academic->period(
            $reportCard->school_id,
            $reportCard->academic_calendar_year_id,
            $reportCard->academic_calendar_term_id,
            $reportCard->school_class_id,
            $reportCard->class_section_id,
        );

        if ($period === null) {
            throw new ReportCardException('Academic frame not found for this run.');
        }

        return $period;
    }

    private function composeEntry(
        ReportCard $reportCard,
        EnrollmentReference $enrollment,
        AcademicPeriodReference $period,
        ?User $actor,
    ): ?AcademicDocumentReference {
        $content = $this->composer->compose(
            $reportCard->school_id,
            $enrollment,
            $period,
            $reportCard->academic_calendar_year_id,
            $reportCard->academic_calendar_term_id,
        );

        $document = $this->communication->generateReportCardDocument(
            $reportCard->school_id,
            $enrollment->student->id,
            $reportCard->academic_calendar_year_id,
            $reportCard->academic_calendar_term_id,
            $reportCard->school_class_id,
            $reportCard->class_section_id,
            sprintf('Bulletin de %s', $enrollment->student->fullName),
            $content,
            $actor,
        );

        ReportCardEntry::updateOrCreate(
            [
                'report_card_id' => $reportCard->getKey(),
                'enrollment_id' => $enrollment->id,
            ],
            [
                'school_id' => $reportCard->school_id,
                'student_id' => $enrollment->student->id,
                'content' => $content,
                'academic_document_id' => $document->id,
                'academic_document_number' => $document->documentNumber,
                'status' => ReportCardEntryStatus::Included,
            ],
        );

        return $document;
    }
}
