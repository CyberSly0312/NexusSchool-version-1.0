<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Application\Actions;

use App\Domains\Gradebook\Domain\Enums\GradebookStatus;
use App\Domains\Gradebook\Domain\Enums\GradeEntryStatus;
use App\Domains\Gradebook\Domain\Events\GradebookLocked;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Domains\Gradebook\Infrastructure\Models\GradeEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Locks a calculated gradebook (SPR-010). Locking freezes the entries: this is
 * the last moment a teacher may alter a figure. It is a prerequisite for
 * publishing the gradebook to the communication engine.
 */
final class LockGradebookAction
{
    public function execute(Gradebook $gradebook, ?User $actor = null): Gradebook
    {
        return DB::transaction(function () use ($gradebook, $actor): Gradebook {
            $this->assertLockable($gradebook);

            GradeEntry::query()
                ->where('gradebook_id', $gradebook->getKey())
                ->where('status', '!=', GradeEntryStatus::Locked)
                ->update(['status' => GradeEntryStatus::Locked]);

            $gradebook->status = GradebookStatus::Locked;
            $gradebook->locked_at = now();
            $gradebook->save();

            GradebookLocked::dispatch($gradebook, $actor);

            return $gradebook;
        });
    }

    private function assertLockable(Gradebook $gradebook): void
    {
        if ($gradebook->status === GradebookStatus::Locked) {
            return;
        }

        $gradebook->assertCanTransitionTo(GradebookStatus::Locked);
    }
}
