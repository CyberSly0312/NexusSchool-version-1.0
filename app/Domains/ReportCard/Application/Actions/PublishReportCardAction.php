<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Application\Actions;

use App\Domains\ReportCard\Domain\Contracts\AcademicCommunicationContract;
use App\Domains\ReportCard\Domain\Enums\ReportCardEntryStatus;
use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;
use App\Domains\ReportCard\Domain\Events\ReportCardPublished;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Publishes the bulletins of a generated run (SPR-011).
 *
 * Each official document is published through the Academic Communication
 * engine (the contract resolves the channels of the document's publication
 * policy). The run then transitions to Published.
 */
final class PublishReportCardAction
{
    public function __construct(
        private readonly AcademicCommunicationContract $communication,
    ) {
    }

    public function execute(ReportCard $reportCard, ?User $actor = null): ReportCard
    {
        return DB::transaction(function () use ($reportCard, $actor): ReportCard {
            $reportCard->assertCanTransitionTo(ReportCardStatus::Published);

            $entries = $reportCard->entries()
                ->where('status', ReportCardEntryStatus::Included)
                ->whereNotNull('academic_document_id')
                ->get();

            foreach ($entries as $entry) {
                $this->communication->publishReportCardDocument(
                    $reportCard->school_id,
                    $entry->academic_document_id,
                    $actor,
                );
            }

            $reportCard->status = ReportCardStatus::Published;
            $reportCard->published_at = now();
            $reportCard->save();

            ReportCardPublished::dispatch($reportCard, $entries->count(), $actor);

            return $reportCard->refresh();
        });
    }
}
