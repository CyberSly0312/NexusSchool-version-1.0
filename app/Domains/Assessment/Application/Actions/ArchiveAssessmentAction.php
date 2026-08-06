<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\Actions;

use App\Domains\Assessment\Domain\Enums\AssessmentStatus;
use App\Domains\Assessment\Domain\Events\AssessmentArchived;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Archives an assessment, removing it from the active flow (SPR-009). Archived
 * assessments are immutable and no longer accept scores.
 */
final class ArchiveAssessmentAction
{
    public function execute(Assessment $assessment, ?User $actor = null): Assessment
    {
        return DB::transaction(function () use ($assessment, $actor): Assessment {
            $assessment->assertCanTransitionTo(AssessmentStatus::Archived);

            $assessment->update([
                'status' => AssessmentStatus::Archived,
                'archived_at' => now(),
            ]);

            AssessmentArchived::dispatch($assessment->refresh(), $actor);

            return $assessment;
        });
    }
}
