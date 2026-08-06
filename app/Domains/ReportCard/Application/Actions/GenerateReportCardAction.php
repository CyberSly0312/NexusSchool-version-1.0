<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Application\Actions;

use App\Domains\ReportCard\Application\Services\ReportCardContentComposer;
use App\Domains\ReportCard\Domain\Contracts\AcademicCommunicationContract;
use App\Domains\ReportCard\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\ReportCard\Domain\Contracts\EnrollmentContract;
use App\Domains\ReportCard\Domain\Enums\ReportCardEntryStatus;
use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;
use App\Domains\ReportCard\Domain\Events\ReportCardGenerated;
use App\Domains\ReportCard\Domain\References\AcademicDocumentReference;
use App\Domains\ReportCard\Domain\References\AcademicPeriodReference;
use App\Domains\ReportCard\Domain\References\EnrollmentReference;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Domains\ReportCard\Infrastructure\Models\ReportCardEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Generates the bulletins of a report card run (SPR-011).
 *
 * Composes the snapshot of every active student of the run's class from the
 * validated upstream aggregates, delegates the official document to the
 * Academic Communication engine through the contract, and transitions the run
 * to Generated. The engine never performs any calculation.
 */
final class GenerateReportCardAction
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

            ReportCardGenerated::dispatch(
                $reportCard,
                $documentNumbers,
                count($documentNumbers),
                $actor,
            );

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
            throw new \App\Domains\ReportCard\Domain\Exceptions\ReportCardException('Academic frame not found for this run.');
        }

        return $period;
    }

    /**
     * Composes one student's bulletin, produces its official document and
     * persists the entry. Returns null when no document could be produced.
     */
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
