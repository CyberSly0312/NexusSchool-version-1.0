<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Infrastructure\Contracts;

use App\Domains\CertificateAndTranscript\Domain\Contracts\StudentIdentityContract;
use App\Domains\CertificateAndTranscript\Domain\References\StudentReference;
use App\Domains\Student\Domain\Enums\Gender;
use App\Domains\Student\Infrastructure\Models\Student;

/**
 * StudentIdentityContract implementation (SPR-015).
 *
 * Maps the Student model onto the completion student identity. This is the only
 * place in the domain allowed to see a Student model.
 */
final class StudentIdentityAdapter implements StudentIdentityContract
{
    public function student(string $schoolId, int|string $studentId): ?StudentReference
    {
        $student = Student::query()->forSchool($schoolId)->find($studentId);

        if ($student === null) {
            return null;
        }

        $dateOfBirth = $student->getAttribute('date_of_birth');
        if ($dateOfBirth instanceof \DateTimeInterface) {
            $dateOfBirth = $dateOfBirth->format('Y-m-d');
        }
        $dateOfBirth = $dateOfBirth === null ? null : (string) $dateOfBirth;

        $gender = $student->getAttribute('gender');
        $gender = $gender instanceof Gender ? $gender->value : ($gender === null ? null : (string) $gender);

        return new StudentReference(
            id: $student->getKey(),
            fullName: (string) $student->full_name,
            studentNumber: (string) $student->student_number,
            firstName: $student->first_name,
            lastName: $student->last_name,
            class: $student->schoolClass?->getAttribute('name'),
            section: $student->section?->getAttribute('name'),
            dateOfBirth: $dateOfBirth,
            gender: $gender,
            userId: $student->user_id,
        );
    }
}
