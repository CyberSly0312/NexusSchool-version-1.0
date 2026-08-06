<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Application\Actions;

use App\Domains\Gradebook\Domain\Enums\GradebookStatus;
use App\Domains\Gradebook\Domain\Events\GradebookArchived;
use App\Domains\Gradebook\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Archives a gradebook at the end of its usable life (SPR-010). Archived
 * gradebooks disappear from the active flow but their figures stay readable by
 * the communication engine for historical documents.
 */
final class ArchiveGradebookAction
{
    public function execute(Gradebook $gradebook, ?User $actor = null): void
    {
        DB::transaction(function () use ($gradebook, $actor): void {
            if (! GradebookStatus::Archived->allowsTransitionFrom($gradebook->status)) {
                throw InvalidStatusTransitionException::from($gradebook->status, GradebookStatus::Archived);
            }

            $gradebook->status = GradebookStatus::Archived;
            $gradebook->archived_at = now();
            $gradebook->save();

            GradebookArchived::dispatch($gradebook, $actor);
        });
    }
}
