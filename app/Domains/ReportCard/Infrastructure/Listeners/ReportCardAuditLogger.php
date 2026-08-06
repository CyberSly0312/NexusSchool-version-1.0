<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Infrastructure\Listeners;

use App\Domains\ReportCard\Domain\Events\ReportCardArchived;
use App\Domains\ReportCard\Domain\Events\ReportCardCreated;
use App\Domains\ReportCard\Domain\Events\ReportCardGenerated;
use App\Domains\ReportCard\Domain\Events\ReportCardPublished;
use App\Domains\ReportCard\Domain\Events\ReportCardRegenerated;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Writes the security audit trail (AuditLog) for every Report Card event
 * (SPR-011): creation, generation, regeneration, publishing and archiving.
 */
final class ReportCardAuditLogger
{
    public function handleCreated(ReportCardCreated $event): void
    {
        $this->log(
            $event->reportCard,
            'report_card_created',
            "Report card run [{$event->reportCard->displayName()}] created",
            $event->actor,
            $this->payload($event->reportCard),
        );
    }

    public function handleGenerated(ReportCardGenerated $event): void
    {
        $this->log(
            $event->reportCard,
            'report_card_generated',
            "Report card run [{$event->reportCard->displayName()}] generated with {$event->includedEntries} bulletins",
            $event->actor,
            $this->payload($event->reportCard, [
                'included_entries' => $event->includedEntries,
                'document_numbers' => $event->documentNumbers,
            ]),
        );
    }

    public function handleRegenerated(ReportCardRegenerated $event): void
    {
        $this->log(
            $event->reportCard,
            'report_card_regenerated',
            "Report card run [{$event->reportCard->displayName()}] regenerated",
            $event->actor,
            $this->payload($event->reportCard, [
                'document_numbers' => $event->documentNumbers,
            ]),
        );
    }

    public function handlePublished(ReportCardPublished $event): void
    {
        $this->log(
            $event->reportCard,
            'report_card_published',
            "Report card run [{$event->reportCard->displayName()}] published with {$event->publishedEntries} bulletins",
            $event->actor,
            $this->payload($event->reportCard, [
                'published_entries' => $event->publishedEntries,
            ]),
        );
    }

    public function handleArchived(ReportCardArchived $event): void
    {
        $this->log(
            $event->reportCard,
            'report_card_archived',
            "Report card run [{$event->reportCard->displayName()}] archived",
            $event->actor,
            $this->payload($event->reportCard),
        );
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function log(
        ReportCard $reportCard,
        string $action,
        string $description,
        ?User $actor = null,
        ?array $payload = null,
    ): void {
        AuditLog::create([
            'school_id' => $reportCard->school_id,
            'auditable_type' => $reportCard::class,
            'auditable_id' => $reportCard->getKey(),
            'action' => $action,
            'event' => class_basename($reportCard),
            'description' => $description,
            'user_id' => $actor?->getKey(),
            'user_type' => $actor !== null ? $actor::class : null,
            'new_values' => $payload,
        ]);
    }

    /**
     * @param  array<string, mixed>  $extra
     *
     * @return array<string, mixed>
     */
    private function payload(ReportCard $reportCard, array $extra = []): array
    {
        return array_merge([
            'year_id' => $reportCard->academic_calendar_year_id,
            'term_id' => $reportCard->academic_calendar_term_id,
            'class_id' => $reportCard->school_class_id,
            'section_id' => $reportCard->class_section_id,
            'type' => $reportCard->report_card_type?->value,
            'status' => $reportCard->status->value,
        ], $extra);
    }
}
