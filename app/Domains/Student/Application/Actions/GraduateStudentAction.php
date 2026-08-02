<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\Actions;

use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\Events\StudentGraduated;
use App\Domains\Student\Domain\Exceptions\InvalidStudentStateException;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class GraduateStudentAction
{
    public function execute(Student $student, ?User $actor = null): Student
    {
        if ($student->isGraduated()) {
            throw InvalidStudentStateException::for(
                'graduate',
                $student->status->value,
                'A student cannot graduate twice.',
            );
        }

        if (! $student->isActive()) {
            throw InvalidStudentStateException::for(
                'graduate',
                $student->status->value,
                'Only active students can graduate.',
            );
        }

        return DB::transaction(function () use ($student, $actor): Student {
            $student->update([
                'status' => StudentStatus::Graduated,
                'graduated_at' => now()->toDateString(),
            ]);
            $student->refresh();

            StudentGraduated::dispatch($student, $actor);

            return $student;
        });
    }
}
