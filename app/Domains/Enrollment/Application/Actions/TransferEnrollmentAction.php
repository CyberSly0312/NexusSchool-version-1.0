<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\Actions;

use App\Domains\Enrollment\Application\DTOs\TransferData;
use App\Domains\Enrollment\Application\Services\EnrollmentAcademicResolver;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Enums\TransferType;
use App\Domains\Enrollment\Domain\Events\StudentTransferred;
use App\Domains\Enrollment\Domain\Exceptions\EnrollmentStateException;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentTransfer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Handles internal (class/section) transfers, which keep the enrollment active
 * and update its assignment, and outgoing school transfers, which archive the
 * enrollment. Transfers preserve the full history of the enrollment.
 */
final class TransferEnrollmentAction
{
    public function __construct(
        private readonly EnrollmentAcademicResolver $resolver,
    ) {
    }

    public function execute(Enrollment $enrollment, TransferData $data, ?User $actor = null): Enrollment
    {
        if ($enrollment->isImmutable() || ! $enrollment->isActive()) {
            throw EnrollmentStateException::transition($enrollment->status, 'transferred');
        }

        $fromClass = $enrollment->schoolClass()->first();

        if ($data->transferType === TransferType::Internal) {
            $this->assertInternalTransferData($enrollment, $data);
        }

        return DB::transaction(function () use ($enrollment, $data, $fromClass, $actor): Enrollment {
            $toClass = null;
            $fromSectionId = $enrollment->class_section_id;

            if ($data->transferType === TransferType::Internal && $data->toClassId !== null) {
                $toClass = $this->resolver->assertClassInSchool($enrollment->school_id, $data->toClassId);

                $enrollment->school_class_id = $toClass->getKey();
                $enrollment->academic_program_id = $toClass->academic_program_id;
                $enrollment->education_level_id = $toClass->education_level_id;
            }

            if ($data->toSectionId !== null) {
                $enrollment->class_section_id = $data->toSectionId;
            }

            if ($data->transferType === TransferType::SchoolTransfer) {
                $enrollment->status = EnrollmentStatus::Archived;
                $enrollment->exited_at = $data->transferDate;
            }

            $enrollment->save();

            $transfer = EnrollmentTransfer::create([
                'school_id' => $enrollment->school_id,
                'enrollment_id' => $enrollment->getKey(),
                'student_id' => $enrollment->student_id,
                'transfer_type' => $data->transferType->value,
                'from_class_id' => $fromClass?->getKey(),
                'to_class_id' => $toClass?->getKey(),
                'from_section_id' => $fromSectionId,
                'to_section_id' => $data->toSectionId,
                'from_school_id' => $data->fromSchoolId,
                'to_school_id' => $data->toSchoolId,
                'reason' => $data->reason,
                'transfer_date' => $data->transferDate,
                'notes' => $data->notes,
            ]);

            StudentTransferred::dispatch($enrollment, $transfer, $actor);

            return $enrollment;
        });
    }

    private function assertInternalTransferData(Enrollment $enrollment, TransferData $data): void
    {
        if ($data->toClassId === null && $data->toSectionId === null) {
            throw new InvalidArgumentException(
                'An internal transfer requires a target class or a target section.',
            );
        }

        if ($data->toClassId !== null) {
            $toClass = $this->resolver->assertClassInSchool($enrollment->school_id, $data->toClassId);
            $this->resolver->assertSectionBelongsToClass($toClass, $data->toSectionId);
        }
    }
}
