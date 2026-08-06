<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Application\Actions;

use App\Domains\Gradebook\Domain\Enums\GradebookStatus;
use App\Domains\Gradebook\Domain\Enums\GradeEntryStatus;
use App\Domains\Gradebook\Domain\Events\GradebookUnlocked;
use App\Domains\Gradebook\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Domains\Gradebook\Infrastructure\Models\GradeEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Re-opens a locked gradebook for correction (SPR-010). Unlocking moves the
 * gradebook back to calculated and relaxes the entries to validated, so a
 * recalculation can refresh the figures. This is an audited, explicit action.
 */
final class UnlockGradebookAction
{
    public function execute(Gradebook $gradebook, ?User $actor = null): Gradebook
    {
        return DB::transaction(function () use ($gradebook, $actor): Gradebook {
            if ($gradebook->status !== GradebookStatus::Locked) {
                throw InvalidStatusTransitionException::from($gradebook->status, GradebookStatus::Calculated);
            }

            GradeEntry::query()
                ->where('gradebook_id', $gradebook->getKey())
                ->where('status', '!=', GradeEntryStatus::Validated)
                ->update(['status' => GradeEntryStatus::Validated]);

            $gradebook->status = GradebookStatus::Calculated;
            $gradebook->locked_at = null;
            $gradebook->save();

            GradebookUnlocked::dispatch($gradebook, $actor);

            return $gradebook;
        });
    }
}
