<?php

declare(strict_types=1);

namespace App\Domains\StudentEnrollment\Rules;

use App\Domains\StudentEnrollment\Enums\EnrollmentStatus;
use App\Domains\StudentEnrollment\Enums\EnrollmentType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StudentEnrollmentRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'academic_class_id' => ['required', 'integer', 'exists:academic_classes,id'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'grade_level_id' => ['required', 'integer', 'exists:grade_levels,id'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'classroom_id' => ['nullable', 'integer', 'exists:classrooms,id'],
            'academic_term_id' => ['nullable', 'integer', 'exists:academic_terms,id'],
            'enrollment_number' => ['required', 'string', 'max:50'],
            'enrollment_date' => ['required', 'date'],
            'effective_date' => ['required', 'date', 'after_or_equal:enrollment_date'],
            'enrollment_type' => ['required', 'string', 'in:'.implode(',', array_map(fn ($case) => $case->value, EnrollmentType::cases()))],
            'status' => ['sometimes', 'string', 'in:'.implode(',', array_map(fn ($case) => $case->value, EnrollmentStatus::cases()))],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'transfer_reason' => ['nullable', 'string', 'max:500'],
            'exit_date' => ['nullable', 'date', 'after_or_equal:enrollment_date'],
            'exit_reason' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public static function update(?int $enrollmentId = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'student_id' => ['sometimes', 'required', 'integer', 'exists:students,id'],
            'academic_year_id' => ['sometimes', 'required', 'integer', 'exists:academic_years,id'],
            'academic_class_id' => ['sometimes', 'required', 'integer', 'exists:academic_classes,id'],
            'program_id' => ['sometimes', 'required', 'integer', 'exists:programs,id'],
            'grade_level_id' => ['sometimes', 'required', 'integer', 'exists:grade_levels,id'],
            'section_id' => ['sometimes', 'required', 'integer', 'exists:sections,id'],
            'classroom_id' => ['nullable', 'integer', 'exists:classrooms,id'],
            'academic_term_id' => ['nullable', 'integer', 'exists:academic_terms,id'],
            'enrollment_number' => ['sometimes', 'required', 'string', 'max:50'],
            'enrollment_date' => ['sometimes', 'required', 'date'],
            'effective_date' => ['sometimes', 'required', 'date', 'after_or_equal:enrollment_date'],
            'enrollment_type' => ['sometimes', 'required', 'string', 'in:'.implode(',', array_map(fn ($case) => $case->value, EnrollmentType::cases()))],
            'status' => ['sometimes', 'string', 'in:'.implode(',', array_map(fn ($case) => $case->value, EnrollmentStatus::cases()))],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'transfer_reason' => ['nullable', 'string', 'max:500'],
            'exit_date' => ['nullable', 'date', 'after_or_equal:enrollment_date'],
            'exit_reason' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $enrollmentId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($enrollmentId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
