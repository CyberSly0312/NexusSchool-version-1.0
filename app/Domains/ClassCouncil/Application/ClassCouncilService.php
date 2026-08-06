<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Application;

use App\Domains\ClassCouncil\Application\Actions\ArchiveCouncilSessionAction;
use App\Domains\ClassCouncil\Application\Actions\ConveneCouncilSessionAction;
use App\Domains\ClassCouncil\Application\Actions\CreateCouncilSessionAction;
use App\Domains\ClassCouncil\Application\Actions\PublishCouncilSessionAction;
use App\Domains\ClassCouncil\Application\Actions\RecordCouncilDecisionAction;
use App\Domains\ClassCouncil\Application\Actions\RecordCouncilDeliberationAction;
use App\Domains\ClassCouncil\Application\DTOs\CreateCouncilSessionData;
use App\Domains\ClassCouncil\Application\DTOs\RecordCouncilDecisionData;
use App\Domains\ClassCouncil\Application\DTOs\RecordCouncilDeliberationData;
use App\Domains\ClassCouncil\Domain\References\CouncilReviewReference;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDecision;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilDeliberation;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Models\User;

/**
 * Application facade for the Class Council engine (SPR-013). Delegates to the
 * action objects so feature calls stay single-line and actions stay testable.
 */
final class ClassCouncilService
{
    public function __construct(
        private readonly CreateCouncilSessionAction $createSession,
        private readonly ConveneCouncilSessionAction $conveneSession,
        private readonly RecordCouncilDecisionAction $recordDecision,
        private readonly RecordCouncilDeliberationAction $recordDeliberation,
        private readonly PublishCouncilSessionAction $publishSession,
        private readonly ArchiveCouncilSessionAction $archiveSession,
    ) {
    }

    public function createSession(CreateCouncilSessionData $data, ?User $actor = null): CouncilSession
    {
        return $this->createSession->execute($data, $actor);
    }

    /**
     * @return list<CouncilReviewReference>
     */
    public function conveneSession(CouncilSession $session, ?User $actor = null): array
    {
        return $this->conveneSession->execute($session, $actor);
    }

    public function recordDecision(RecordCouncilDecisionData $data, ?User $actor = null): CouncilDecision
    {
        return $this->recordDecision->execute($data, $actor);
    }

    public function recordDeliberation(RecordCouncilDeliberationData $data, ?User $actor = null): CouncilDeliberation
    {
        return $this->recordDeliberation->execute($data, $actor);
    }

    public function publishSession(CouncilSession $session, ?User $actor = null): CouncilSession
    {
        return $this->publishSession->execute($session, $actor);
    }

    public function archiveSession(CouncilSession $session, ?User $actor = null): void
    {
        $this->archiveSession->execute($session, $actor);
    }
}
