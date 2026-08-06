<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Application\Actions;

use App\Domains\Gradebook\Domain\Enums\GradebookStatus;
use App\Domains\Gradebook\Domain\Enums\GradeEntryStatus;
use App\Domains\Gradebook\Domain\Events\GradebookPublished;
use App\Domains\Gradebook\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Domains\Gradebook\Infrastructure\Models\GradeEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Publishes a locked gradebook (SPR-010). Published entries are the only ones
 * the communication engine reads for report cards and class summaries.
 */
final class PublishGradebookAction
{
    public function execute(Gradebook $gradebook, ?User $actor = null): Gradebook
    {
        return DB::transaction(function () use ($gradebook, $actor): Gradebook {
            if (! GradebookStatus::Published->allowsTransitionFrom($gradebook->status)) {
                throw InvalidStatusTransitionException::from($gradebook->status, GradebookStatus::Published);
            }

            GradeEntry::query()
                ->where('gradebook_id', $gradebook->getKey())
                ->where('status', '!=', GradeEntryStatus::Published)
                ->update(['status' => GradeEntryStatus::Published]);

            $gradebook->status = GradebookStatus::Published;
            $gradebook->published_at = now();
            $gradebook->save();

            GradebookPublished::dispatch($gradebook, $actor);

            return $gradebook;
        });
    }
}
