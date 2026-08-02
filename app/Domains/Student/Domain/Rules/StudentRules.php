<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\Rules;

use App\Domains\Student\Domain\Enums\Gender;
use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Domain\Enums\TransferStatus;

/**
 * Validation vocabulary for the Student aggregate. Rules are declared once here
 * and reused by the Filament forms and by the application actions' pre-checks.
 */
final class StudentRules
{
    /**
     * @param  int|string|null  $ignoreStudentId
     * @return array<string, array<int, mixed>>
     */
    public static function create(?string $schoolId = null, int|string|null $ignoreStudentId = null): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'preferred_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:'.implode(',', array_column(Gender::cases(), 'value'))],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'nationality' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255', self::uniqueEmail($schoolId, $ignoreStudentId)],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'student_number' => ['required', 'string', 'max:50', self::uniqueNumber($schoolId, $ignoreStudentId)],
            'admission_number' => ['nullable', 'string', 'max:50', self::uniqueAdmissionNumber($schoolId, $ignoreStudentId)],
            'admission_date' => ['required', 'date'],
            'national_id' => ['nullable', 'string', 'max:100'],
            'birth_certificate_reference' => ['nullable', 'string', 'max:100'],
            'academic_calendar_year_id' => ['required', 'integer'],
            'school_class_id' => ['required', 'integer'],
            'class_section_id' => ['nullable', 'integer'],
            'photo' => ['nullable', 'string', 'max:255'],
            'medical_notes' => ['nullable', 'string'],
            'status' => ['required', 'in:'.implode(',', array_column(StudentStatus::cases(), 'value'))],
            'transfer_status' => ['nullable', 'in:'.implode(',', array_column(TransferStatus::cases(), 'value'))],
        ];
    }

    /**
     * @param  int|string  $ignoreStudentId
     * @return array<string, array<int, mixed>>
     */
    public static function update(string $schoolId, int|string $ignoreStudentId): array
    {
        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'preferred_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['sometimes', 'required', 'in:'.implode(',', array_column(Gender::cases(), 'value'))],
            'date_of_birth' => ['sometimes', 'required', 'date', 'before:today'],
            'nationality' => ['sometimes', 'required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255', self::uniqueEmail($schoolId, $ignoreStudentId)],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'student_number' => ['sometimes', 'required', 'string', 'max:50', self::uniqueNumber($schoolId, $ignoreStudentId)],
            'admission_number' => ['nullable', 'string', 'max:50', self::uniqueAdmissionNumber($schoolId, $ignoreStudentId)],
            'admission_date' => ['sometimes', 'required', 'date'],
            'national_id' => ['nullable', 'string', 'max:100'],
            'birth_certificate_reference' => ['nullable', 'string', 'max:100'],
            'academic_calendar_year_id' => ['sometimes', 'required', 'integer'],
            'school_class_id' => ['sometimes', 'required', 'integer'],
            'class_section_id' => ['nullable', 'integer'],
            'photo' => ['nullable', 'string', 'max:255'],
            'medical_notes' => ['nullable', 'string'],
        ];
    }

    private static function uniqueNumber(?string $schoolId, int|string|null $ignoreStudentId = null): mixed
    {
        return $schoolId !== null
            ? RuleUnique::studentNumber($schoolId, $ignoreStudentId)
            : 'unique:students,student_number';
    }

    private static function uniqueEmail(?string $schoolId, int|string|null $ignoreStudentId = null): mixed
    {
        return $schoolId !== null
            ? RuleUnique::email($schoolId, $ignoreStudentId)
            : 'unique:students,email';
    }

    private static function uniqueAdmissionNumber(?string $schoolId, int|string|null $ignoreStudentId = null): mixed
    {
        return $schoolId !== null
            ? RuleUnique::admissionNumber($schoolId, $ignoreStudentId)
            : 'nullable';
    }
}
