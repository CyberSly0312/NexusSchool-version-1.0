<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Contracts;

use App\Domains\AcademicCommunication\Domain\Contracts\GuardianContract;
use App\Domains\AcademicCommunication\Domain\References\GuardianReference;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;

/**
 * Maps Guardian-domain models onto the GuardianContract (SPR-012). Guardians
 * only ever receive the data of the children they are linked to; this adapter
 * exposes the current, notification-worthy guardians of a student.
 */
final class GuardianAdapter implements GuardianContract
{
    public function guardiansForStudent(string $schoolId, int|string $studentId): array
    {
        $guardianIds = StudentGuardianRelationship::query()
            ->forSchool($schoolId)
            ->where('student_id', $studentId)
            ->get()
            ->filter(fn (StudentGuardianRelationship $link): bool => $link->isCurrent())
            ->pluck('guardian_id')
            ->all();

        if ($guardianIds === []) {
            return [];
        }

        return Guardian::query()
            ->forSchool($schoolId)
            ->whereKey($guardianIds)
            ->orderByDesc('is_primary_guardian')
            ->orderBy('last_name')
            ->get()
            ->map(fn (Guardian $guardian): GuardianReference => new GuardianReference(
                id: $guardian->getKey(),
                fullName: (string) $guardian->full_name,
                guardianNumber: $guardian->guardian_number,
                email: $guardian->email,
                phone: $guardian->phone_number,
                isPrimary: (bool) $guardian->is_primary_guardian,
                userId: $guardian->user_id,
            ))
            ->all();
    }
}
