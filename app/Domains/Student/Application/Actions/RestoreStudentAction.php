<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\Actions;

use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\Events\StudentRestored;
use App\Domains\Student\Domain\Exceptions\InvalidStudentStateException;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Restores a previously archived student back to an active state.
 */
final class RestoreStudentAction
{
    public function execute(Student $student, ?User $actor = null): Student
    {
        if (! $student->isArchived()) {
            throw InvalidStudentStateException::for(
                'restore',
                $student->status->value,
                'Only archived students can be restored.',
            );
        }

        return DB::transaction(function () use ($student, $actor): Student {
            $student->restore();

            $student->update([
                'status' => StudentStatus::Active,
                'archived_at' => null,
            ]);

            $student->refresh();

            StudentRestored::dispatch($student, $actor);

            return $student;
        });
    }
}
