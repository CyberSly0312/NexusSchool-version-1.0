<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\Actions;

use App\Domains\Assessment\Domain\Enums\AssessmentStatus;
use App\Domains\Assessment\Domain\Events\AssessmentPublished;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Publishes a draft or scheduled assessment (SPR-009). Publishing freezes the
 * definition; scores remain recordable until the assessment is completed.
 */
final class PublishAssessmentAction
{
    public function execute(Assessment $assessment, ?User $actor = null): Assessment
    {
        return DB::transaction(function () use ($assessment, $actor): Assessment {
            $assessment->assertCanTransitionTo(AssessmentStatus::Published);

            $assessment->update([
                'status' => AssessmentStatus::Published,
                'published_at' => now(),
            ]);

            AssessmentPublished::dispatch($assessment->refresh(), $actor);

            return $assessment;
        });
    }
}
