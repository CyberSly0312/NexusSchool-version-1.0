<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Application\Actions;

use App\Domains\ReportCard\Domain\Contracts\RecordsLifecycleContract;
use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;
use App\Domains\ReportCard\Domain\Events\ReportCardArchived;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Archives a generated or published run (SPR-011). Archived runs are kept for
 * audit and removed from the active flow.
 */
final class ArchiveReportCardAction
{
    public function __construct(
        private readonly RecordsLifecycleContract $lifecycle,
    ) {
    }

    public function execute(ReportCard $reportCard, ?User $actor = null): void
    {
        DB::transaction(function () use ($reportCard, $actor): void {
            $reportCard->assertCanTransitionTo(ReportCardStatus::Archived);

            $reportCard->status = ReportCardStatus::Archived;
            $reportCard->archived_at = now();
            $reportCard->save();

            $this->lifecycle->archive(
                $reportCard->school_id,
                $reportCard->getKey(),
                $actor,
            );

            ReportCardArchived::dispatch($reportCard, $actor);
        });
    }
}
