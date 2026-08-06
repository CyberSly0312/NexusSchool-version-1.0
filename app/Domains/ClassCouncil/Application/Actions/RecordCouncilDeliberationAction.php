<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Application\Actions;

use App\Domains\ClassCouncil\Application\DTOs\RecordCouncilDeliberationData;
use App\Domains\ClassCouncil\Domain\Exceptions\ClassCouncilException;
use App\Domains\ClassCouncil\Domain\Exceptions\CouncilSessionLockedException;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDeliberation;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Records a confidential deliberation note of a convened council session
 * (SPR-013). Deliberation records are never exposed through a contract and can
 * never reach a rendered document — they are the private record of the council.
 */
final class RecordCouncilDeliberationAction
{
    public function execute(RecordCouncilDeliberationData $data, ?User $actor = null): CouncilDeliberation
    {
        return DB::transaction(function () use ($data, $actor): CouncilDeliberation {
            $session = $this->requireDeliberatingSession($data);

            return CouncilDeliberation::create([
                'school_id' => $data->schoolId,
                'council_session_id' => $session->getKey(),
                'student_id' => $data->studentId,
                'council_decision_id' => $data->decisionId,
                'member' => $data->member,
                'topic' => $data->topic,
                'summary' => $data->summary,
                'noted_at' => $data->notedAt ?? now(),
                'recorded_by' => $actor?->getKey(),
            ]);
        });
    }

    private function requireDeliberatingSession(RecordCouncilDeliberationData $data): CouncilSession
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

        return $session;
    }
}
