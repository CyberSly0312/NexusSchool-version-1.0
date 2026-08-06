<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Contracts;

use App\Domains\AcademicCommunication\Domain\Contracts\SchoolContract;
use App\Domains\AcademicCommunication\Domain\References\SchoolReference;
use App\Domains\School\Models\School;

/**
 * Maps the School (tenant) record onto the SchoolContract (SPR-012). This is
 * the only place in the Communication domain allowed to see the School model.
 */
final class SchoolAdapter implements SchoolContract
{
    public function school(string $schoolId): ?SchoolReference
    {
        $school = School::query()->find($schoolId);

        return $school !== null ? $this->toSchool($school) : null;
    }

    private function toSchool(School $school): SchoolReference
    {
        return new SchoolReference(
            id: (string) $school->getKey(),
            name: (string) $school->name,
            logoUrl: $school->logo_url,
            address: $school->address,
            city: $school->city,
            phone: $school->phone,
            email: $school->email,
            registrationNumber: $school->registration_number,
            schoolCode: $school->slug,
            settings: $school->settings ?? [],
        );
    }
}
