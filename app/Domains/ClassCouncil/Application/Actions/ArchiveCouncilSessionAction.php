<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Application\Actions;

use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionArchived;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Archives a published council session (SPR-013). Archiving preserves the
 * decisions and removes the session from the active flow; the decisions stay
 * readable through the contracts as published academic history.
 */
final class ArchiveCouncilSessionAction
{
    public function execute(CouncilSession $session, ?User $actor = null): void
    {
        DB::transaction(function () use ($session, $actor): void {
            $session->assertCanTransitionTo(CouncilSessionStatus::Archived);

            $session->status = CouncilSessionStatus::Archived;
            $session->archived_at = now();
            $session->save();

            CouncilSessionArchived::dispatch($session, $actor);
        });
    }
}
