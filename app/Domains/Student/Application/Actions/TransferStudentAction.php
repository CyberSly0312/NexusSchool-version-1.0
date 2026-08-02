<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\Actions;

use App\Domains\Student\Application\DTOs\TransferStudentData;
use App\Domains\Student\Domain\Enums\TransferStatus;
use App\Domains\Student\Domain\Events\StudentTransferred;
use App\Domains\Student\Domain\Exceptions\InvalidStudentStateException;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class TransferStudentAction
{
    public function execute(Student $student, TransferStudentData $data, ?User $actor = null): Student
    {
        if ($student->isImmutable()) {
            throw InvalidStudentStateException::for('transfer', $student->status->value, 'Archived students are immutable.');
        }

        if ($student->isGraduated()) {
            throw InvalidStudentStateException::for('transfer', $student->status->value, 'Graduated students cannot be transferred.');
        }

        return DB::transaction(function () use ($student, $data, $actor): Student {
            $changes = [
                'transfer_status' => $data->transferStatus->value,
                'transferred_at' => $data->transferStatus->isActive() ? now() : null,
                'transfer_from_school' => $data->transferStatus === TransferStatus::Incoming
                    ? ($data->transferFromSchool ?? $student->transfer_from_school)
                    : null,
                'transfer_to_school' => $data->transferStatus === TransferStatus::Outgoing
                    ? ($data->transferToSchool ?? $student->transfer_to_school)
                    : null,
            ];

            if ($data->schoolClassId !== null) {
                $changes['school_class_id'] = $data->schoolClassId;
            }

            $student->update($changes);
            $student->refresh();

            StudentTransferred::dispatch($student, $actor);

            return $student;
        });
    }
}
