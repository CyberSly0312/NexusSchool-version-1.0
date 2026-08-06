<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Application;

use App\Domains\ReportCard\Application\Actions\ArchiveReportCardAction;
use App\Domains\ReportCard\Application\Actions\CreateReportCardAction;
use App\Domains\ReportCard\Application\Actions\GenerateReportCardAction;
use App\Domains\ReportCard\Application\Actions\PublishReportCardAction;
use App\Domains\ReportCard\Application\Actions\RegenerateReportCardAction;
use App\Domains\ReportCard\Application\DTOs\CreateReportCardData;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Models\User;

/**
 * Application facade for the Report Card engine (SPR-011). Delegates to the
 * action objects so feature calls stay single-line and actions stay testable.
 */
final class ReportCardService
{
    public function __construct(
        private readonly CreateReportCardAction $createReportCard,
        private readonly GenerateReportCardAction $generateReportCard,
        private readonly RegenerateReportCardAction $regenerateReportCard,
        private readonly PublishReportCardAction $publishReportCard,
        private readonly ArchiveReportCardAction $archiveReportCard,
    ) {
    }

    public function createReportCard(CreateReportCardData $data, ?User $actor = null): ReportCard
    {
        return $this->createReportCard->execute($data, $actor);
    }

    public function generateReportCard(ReportCard $reportCard, ?User $actor = null): ReportCard
    {
        return $this->generateReportCard->execute($reportCard, $actor);
    }

    public function regenerateReportCard(ReportCard $reportCard, ?User $actor = null): ReportCard
    {
        return $this->regenerateReportCard->execute($reportCard, $actor);
    }

    public function publishReportCard(ReportCard $reportCard, ?User $actor = null): ReportCard
    {
        return $this->publishReportCard->execute($reportCard, $actor);
    }

    public function archiveReportCard(ReportCard $reportCard, ?User $actor = null): void
    {
        $this->archiveReportCard->execute($reportCard, $actor);
    }
}
