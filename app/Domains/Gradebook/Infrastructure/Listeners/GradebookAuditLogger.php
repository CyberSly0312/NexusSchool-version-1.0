<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Infrastructure\Listeners;

use App\Domains\Gradebook\Domain\Events\GradebookArchived;
use App\Domains\Gradebook\Domain\Events\GradebookCalculated;
use App\Domains\Gradebook\Domain\Events\GradebookCreated;
use App\Domains\Gradebook\Domain\Events\GradebookLocked;
use App\Domains\Gradebook\Domain\Events\GradebookPublished;
use App\Domains\Gradebook\Domain\Events\GradebookUnlocked;
use App\Domains\Gradebook\Domain\Events\RankingsCalculated;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Writes the security audit trail (AuditLog) for every Gradebook event
 * (SPR-010): creation, calculation, ranking, locking, unlocking, publishing
 * and archiving.
 */
final class GradebookAuditLogger
{
    public function handleCreated(GradebookCreated $event): void
    {
        $this->log(
            $event->gradebook,
            'gradebook_created',
            "Gradebook [{$event->gradebook->displayName()}] created",
            $event->actor,
            $this->payload($event->gradebook),
        );
    }

    public function handleCalculated(GradebookCalculated $event): void
    {
        $this->log(
            $event->gradebook,
            'gradebook_calculated',
            "Gradebook [{$event->gradebook->displayName()}] calculated with {$event->entryCount} entries",
            $event->actor,
            $this->payload($event->gradebook, ['entries_count' => $event->entryCount]),
        );
    }

    public function handleRankingsCalculated(RankingsCalculated $event): void
    {
        $this->log(
            $event->gradebook,
            'gradebook_rankings_calculated',
            "Rankings recalculated for gradebook [{$event->gradebook->displayName()}]",
            $event->actor,
            $this->payload($event->gradebook),
        );
    }

    public function handleLocked(GradebookLocked $event): void
    {
        $this->log(
            $event->gradebook,
            'gradebook_locked',
            "Gradebook [{$event->gradebook->displayName()}] locked",
            $event->actor,
            $this->payload($event->gradebook),
        );
    }

    public function handleUnlocked(GradebookUnlocked $event): void
    {
        $this->log(
            $event->gradebook,
            'gradebook_unlocked',
            "Gradebook [{$event->gradebook->displayName()}] re-opened for correction",
            $event->actor,
            $this->payload($event->gradebook),
        );
    }

    public function handlePublished(GradebookPublished $event): void
    {
        $this->log(
            $event->gradebook,
            'gradebook_published',
            "Gradebook [{$event->gradebook->displayName()}] published",
            $event->actor,
            $this->payload($event->gradebook),
        );
    }

    public function handleArchived(GradebookArchived $event): void
    {
        $this->log(
            $event->gradebook,
            'gradebook_archived',
            "Gradebook [{$event->gradebook->displayName()}] archived",
            $event->actor,
            $this->payload($event->gradebook),
        );
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function log(
        Gradebook $gradebook,
        string $action,
        string $description,
        ?User $actor = null,
        ?array $payload = null,
    ): void {
        AuditLog::create([
            'school_id' => $gradebook->school_id,
            'auditable_type' => $gradebook::class,
            'auditable_id' => $gradebook->getKey(),
            'action' => $action,
            'event' => class_basename($gradebook),
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
    private function payload(Gradebook $gradebook, array $extra = []): array
    {
        return array_merge([
            'year_id' => $gradebook->academic_calendar_year_id,
            'term_id' => $gradebook->academic_calendar_term_id,
            'subject_id' => $gradebook->academic_subject_id,
            'class_id' => $gradebook->school_class_id,
            'section_id' => $gradebook->class_section_id,
            'coefficient' => $gradebook->coefficient,
            'status' => $gradebook->status->value,
        ], $extra);
    }
}
