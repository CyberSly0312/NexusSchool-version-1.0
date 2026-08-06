<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Application\Actions;

use App\Domains\Timetable\Domain\Events\ConflictResolved;
use App\Domains\Timetable\Domain\Exceptions\ScheduleEntryException;
use App\Domains\Timetable\Infrastructure\Models\TimetableConflict;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Marks a detected conflict as resolved. The conflict row is never deleted so
 * the report history stays complete and auditable.
 */
final class ResolveConflictAction
{
    public function execute(
        int|string $conflictId,
        string $resolution = 'resolved',
        ?string $notes = null,
        ?User $actor = null,
    ): TimetableConflict {
        $conflict = TimetableConflict::query()->find($conflictId);

        if ($conflict === null) {
            throw new ScheduleEntryException('Conflict not found.');
        }

        return DB::transaction(function () use ($conflict, $resolution, $notes, $actor): TimetableConflict {
            $conflict->update([
                'resolved_at' => now(),
                'resolution' => $resolution,
                'resolution_notes' => $notes,
            ]);

            ConflictResolved::dispatch($conflict->refresh(), $actor);

            return $conflict->refresh();
        });
    }
}
