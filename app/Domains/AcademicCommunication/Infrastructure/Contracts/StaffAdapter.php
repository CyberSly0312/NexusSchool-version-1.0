<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Contracts;

use App\Domains\AcademicCommunication\Domain\Contracts\StaffContract;
use App\Domains\AcademicCommunication\Domain\References\StaffReference;
use App\Domains\Staff\Infrastructure\Models\Staff;

/**
 * Maps Staff-domain models onto the StaffContract (SPR-012). Staff are the
 * signatories and the recipients of teaching summaries.
 */
final class StaffAdapter implements StaffContract
{
    public function staff(string $schoolId, int|string $staffId): ?StaffReference
    {
        $staff = Staff::query()->forSchool($schoolId)->find($staffId);

        return $staff !== null ? $this->toStaff($staff) : null;
    }

    public function staffMembers(string $schoolId, array $staffIds): array
    {
        if ($staffIds === []) {
            return [];
        }

        return Staff::query()
            ->forSchool($schoolId)
            ->whereKey($staffIds)
            ->orderBy('last_name')
            ->get()
            ->map(fn (Staff $staff): StaffReference => $this->toStaff($staff))
            ->all();
    }

    private function toStaff(Staff $staff): StaffReference
    {
        return new StaffReference(
            id: $staff->getKey(),
            fullName: (string) $staff->full_name,
            staffNumber: $staff->staff_number,
            specialization: $staff->specialization?->value ?? '',
            email: $staff->email,
            phone: $staff->phone_number,
            userId: $staff->user_id,
        );
    }
}
