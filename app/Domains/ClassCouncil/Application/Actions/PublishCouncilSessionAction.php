<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Application\Actions;

use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionPublished;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Publishes a convened council session (SPR-013).
 *
 * Publishing freezes the session: the decisions become the immutable academic
 * validation of the class and are exposed to the downstream engines (Report
 * Card, Academic Communication). Published sessions are never modified.
 */
final class PublishCouncilSessionAction
{
    public function execute(CouncilSession $session, ?User $actor = null): CouncilSession
    {
        return DB::transaction(function () use ($session, $actor): CouncilSession {
            $session->assertCanTransitionTo(CouncilSessionStatus::Published);

            $decisionsCount = $session->decisions()->count();

            $session->status = CouncilSessionStatus::Published;
            $session->published_at = now();
            $session->save();

            CouncilSessionPublished::dispatch($session, $decisionsCount, $actor);

            return $session->refresh();
        });
    }
}
