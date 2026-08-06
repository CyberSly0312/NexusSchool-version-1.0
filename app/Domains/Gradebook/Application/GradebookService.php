<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Application;

use App\Domains\Gradebook\Application\Actions\ArchiveGradebookAction;
use App\Domains\Gradebook\Application\Actions\CalculateGradebookAction;
use App\Domains\Gradebook\Application\Actions\CreateGradebookAction;
use App\Domains\Gradebook\Application\Actions\LockGradebookAction;
use App\Domains\Gradebook\Application\Actions\PublishGradebookAction;
use App\Domains\Gradebook\Application\Actions\UnlockGradebookAction;
use App\Domains\Gradebook\Application\DTOs\CreateGradebookData;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Models\User;

/**
 * Application facade for the Gradebook engine (SPR-010). Delegates to the
 * action objects so feature calls stay single-line and actions stay testable.
 */
final class GradebookService
{
    public function __construct(
        private readonly CreateGradebookAction $createGradebook,
        private readonly CalculateGradebookAction $calculateGradebook,
        private readonly LockGradebookAction $lockGradebook,
        private readonly UnlockGradebookAction $unlockGradebook,
        private readonly PublishGradebookAction $publishGradebook,
        private readonly ArchiveGradebookAction $archiveGradebook,
    ) {
    }

    public function createGradebook(CreateGradebookData $data, ?User $actor = null): Gradebook
    {
        return $this->createGradebook->execute($data, $actor);
    }

    public function calculateGradebook(Gradebook $gradebook, ?User $actor = null): Gradebook
    {
        return $this->calculateGradebook->execute($gradebook, $actor);
    }

    public function lockGradebook(Gradebook $gradebook, ?User $actor = null): Gradebook
    {
        return $this->lockGradebook->execute($gradebook, $actor);
    }

    public function unlockGradebook(Gradebook $gradebook, ?User $actor = null): Gradebook
    {
        return $this->unlockGradebook->execute($gradebook, $actor);
    }

    public function publishGradebook(Gradebook $gradebook, ?User $actor = null): Gradebook
    {
        return $this->publishGradebook->execute($gradebook, $actor);
    }

    public function archiveGradebook(Gradebook $gradebook, ?User $actor = null): void
    {
        $this->archiveGradebook->execute($gradebook, $actor);
    }
}
