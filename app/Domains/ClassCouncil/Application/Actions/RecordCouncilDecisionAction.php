<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Application\Actions;

use App\Domains\ClassCouncil\Application\DTOs\RecordCouncilDecisionData;
use App\Domains\ClassCouncil\Application\Services\AutomaticDecisionService;
use App\Domains\ClassCouncil\Domain\Contracts\EnrollmentContract;
use App\Domains\ClassCouncil\Domain\Contracts\GradebookContract;
use App\Domains\ClassCouncil\Domain\Events\CouncilDecisionRecorded;
use App\Domains\ClassCouncil\Domain\Exceptions\ClassCouncilException;
use App\Domains\ClassCouncil\Domain\Exceptions\CouncilSessionLockedException;
use App\Domains\ClassCouncil\Domain\Exceptions\OverrideApprovalRequiredException;
use App\Domains\ClassCouncil\Domain\References\AutomaticDecisionReference;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDecision;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Records one council decision (SPR-013).
 *
 * A decision is taken for one student, or for the whole class when no student
 * is given. The session must be convened: published and archived sessions are
 * immutable. An override of the automatic decision requires a justification and
 * an approval; the automatic decision being overridden is snapshotted on the
 * decision for the audit history.
 */
final class RecordCouncilDecisionAction
{
    public function __construct(
        private readonly EnrollmentContract $enrollment,
        private readonly GradebookContract $gradebook,
        private readonly AutomaticDecisionService $automatic,
    ) {
    }

    public function execute(RecordCouncilDecisionData $data, ?User $actor = null): CouncilDecision
    {
        return DB::transaction(function () use ($data, $actor): CouncilDecision {
            $session = $this->requireDeliberatingSession($data);

            $automatic = null;

            if ($data->isOverride) {
                $this->assertOverrideApproved($data);
                $automatic = $this->automaticDecision($data, $session);
            }

            $decision = CouncilDecision::create([
                'school_id' => $data->schoolId,
                'council_session_id' => $session->getKey(),
                'student_id' => $data->studentId,
                'decision_type' => $data->decisionType,
                'category' => $data->category,
                'code' => $this->nextCode($session),
                'label' => $data->label,
                'summary' => $data->summary,
                'recommendation' => $data->recommendation,
                'decision_date' => $data->decisionDate ?? now()->toDateString(),
                'is_override' => $data->isOverride,
                'automatic_decision_type' => $automatic?->type->value,
                'automatic_decision_label' => $automatic?->label(),
                'justification' => $data->justification,
                'approved_by' => $data->approvedBy,
                'approved_at' => $data->isOverride ? now() : null,
                'recorded_by' => $actor?->getKey(),
            ]);

            CouncilDecisionRecorded::dispatch($decision, $actor);

            return $decision;
        });
    }

    private function requireDeliberatingSession(RecordCouncilDecisionData $data): CouncilSession
    {
        $session = CouncilSession::query()
            ->forSchool($data->schoolId)
            ->find($data->sessionId);

        if ($session === null) {
            throw new ClassCouncilException("Council session [{$data->sessionId}] not found.");
        }

        if (! $session->status->isDeliberating()) {
            throw CouncilSessionLockedException::notDeliberating($session->status);
        }

        if ($data->studentId !== null && ! $this->studentBelongsToSession($session, $data->studentId)) {
            throw new ClassCouncilException("Student [{$data->studentId}] is not enrolled in this council's class.");
        }

        return $session;
    }

    private function studentBelongsToSession(CouncilSession $session, int|string $studentId): bool
    {
        return collect($this->enrollment->activeEnrollments(
            $session->school_id,
            $session->academic_calendar_year_id,
            $session->school_class_id,
            $session->class_section_id,
        ))->contains(
            fn ($enrollment): bool => (string) $enrollment->student->id === (string) $studentId,
        );
    }

    private function assertOverrideApproved(RecordCouncilDecisionData $data): void
    {
        if ($data->justification === null || trim($data->justification) === '') {
            throw OverrideApprovalRequiredException::justification();
        }

        if ($data->approvedBy === null) {
            throw OverrideApprovalRequiredException::approval();
        }
    }

    private function automaticDecision(RecordCouncilDecisionData $data, CouncilSession $session): ?AutomaticDecisionReference
    {
        if ($data->studentId === null) {
            return null;
        }

        $standing = $this->gradebook->studentStanding(
            $data->schoolId,
            $data->studentId,
            $session->academic_calendar_year_id,
            $session->academic_calendar_term_id,
        );

        return $this->automatic->propose($standing);
    }

    private function nextCode(CouncilSession $session): string
    {
        $sequence = CouncilDecision::query()
            ->where('council_session_id', $session->getKey())
            ->count() + 1;

        return sprintf('%s-%03d', $session->reference, $sequence);
    }
}
