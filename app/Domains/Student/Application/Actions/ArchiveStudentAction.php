<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\Actions;

use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\Events\StudentArchived;
use App\Domains\Student\Domain\Exceptions\InvalidStudentStateException;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Archives a student record (soft delete + terminal status). Archived records
 * are immutable and excluded from active queries.
 */
final class ArchiveStudentAction
{
    public function execute(Student $student, ?User $actor = null): Student
    {
        if ($student->isArchived()) {
            throw InvalidStudentStateException::for(
                'archive',
                $student->status->value,
                'The student is already archived.',
            );
        }

        return DB::transaction(function () use ($student, $actor): Student {
            $student->update([
                'status' => StudentStatus::Archived,
                'archived_at' => now(),
            ]);

            $student->delete();

            $student->refresh();

            StudentArchived::dispatch($student, $actor);

            return $student;
        });
    }
}
