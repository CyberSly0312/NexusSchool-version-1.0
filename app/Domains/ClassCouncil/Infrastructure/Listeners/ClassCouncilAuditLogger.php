<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Infrastructure\Listeners;

use App\Domains\ClassCouncil\Domain\Events\CouncilDecisionRecorded;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionArchived;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionConvened;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionCreated;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionPublished;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Writes the security audit trail (AuditLog) for every Class Council event
 * (SPR-013): session creation, convening, decision recording (with the override
 * justification and approval), publishing and archiving.
 */
final class ClassCouncilAuditLogger
{
    public function handleCreated(CouncilSessionCreated $event): void
    {
        $this->log(
            $event->session,
            'council_session_created',
            "Council session [{$event->session->displayName()}] created",
            $event->actor,
            $this->payload($event->session),
        );
    }

    public function handleConvened(CouncilSessionConvened $event): void
    {
        $this->log(
            $event->session,
            'council_session_convened',
            "Council session [{$event->session->displayName()}] convened with ".count($event->reviews).' students to review',
            $event->actor,
            $this->payload($event->session, [
                'students_to_review' => count($event->reviews),
            ]),
        );
    }

    public function handleDecisionRecorded(CouncilDecisionRecorded $event): void
    {
        $decision = $event->decision;

        $extra = [
            'decision_code' => $decision->code,
            'decision_type' => $decision->decision_type?->value,
            'category' => $decision->category?->value,
            'student_id' => $decision->student_id,
            'is_override' => $decision->is_override,
            'automatic_decision_type' => $decision->automatic_decision_type,
            'approved_by' => $decision->approved_by,
        ];

        if ($decision->is_override) {
            $extra['justification'] = $decision->justification;
        }

        $session = $decision->session;

        $this->log(
            $session,
            'council_decision_recorded',
            "Council decision [{$decision->code}] recorded".($decision->is_override ? ' as an override' : ''),
            $event->actor,
            $this->payload($session, $extra),
        );
    }

    public function handlePublished(CouncilSessionPublished $event): void
    {
        $this->log(
            $event->session,
            'council_session_published',
            "Council session [{$event->session->displayName()}] published with {$event->decisionsCount} decisions",
            $event->actor,
            $this->payload($event->session, [
                'decisions_count' => $event->decisionsCount,
            ]),
        );
    }

    public function handleArchived(CouncilSessionArchived $event): void
    {
        $this->log(
            $event->session,
            'council_session_archived',
            "Council session [{$event->session->displayName()}] archived",
            $event->actor,
            $this->payload($event->session),
        );
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function log(
        CouncilSession $session,
        string $action,
        string $description,
        ?User $actor = null,
        ?array $payload = null,
    ): void {
        AuditLog::create([
            'school_id' => $session->school_id,
            'auditable_type' => $session::class,
            'auditable_id' => $session->getKey(),
            'action' => $action,
            'event' => class_basename($session),
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
    private function payload(CouncilSession $session, array $extra = []): array
    {
        return array_merge([
            'reference' => $session->reference,
            'year_id' => $session->academic_calendar_year_id,
            'term_id' => $session->academic_calendar_term_id,
            'class_id' => $session->school_class_id,
            'section_id' => $session->class_section_id,
            'status' => $session->status->value,
        ], $extra);
    }
}
