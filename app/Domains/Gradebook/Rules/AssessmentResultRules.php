<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Rules;

use App\Domains\Gradebook\Enums\AssessmentResultStatus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AssessmentResultRules
{
    private static function baseRules(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'student_enrollment_id' => ['nullable', 'integer', 'exists:student_enrollments,id'],
            'assessment_id' => ['required', 'integer', 'exists:assessments,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'academic_term_id' => ['required', 'integer', 'exists:academic_terms,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'raw_score' => ['required', 'numeric', 'min:0'],
            'adjusted_score' => ['nullable', 'numeric', 'min:0'],
            'maximum_score' => ['required', 'numeric', 'min:0'],
            'percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'letter_grade' => ['nullable', 'string', 'max:5'],
            'grade_point' => ['nullable', 'numeric', 'min:0', 'max:4.5'],
            'coefficient' => ['required', 'numeric', 'min:0.01', 'max:99.99'],
            'weighted_score' => ['nullable', 'numeric', 'min:0'],
            'rank_in_assessment' => ['nullable', 'integer', 'min:1'],
            'is_absent' => ['boolean'],
            'is_excused' => ['boolean'],
            'is_published' => ['boolean'],
            'is_locked' => ['boolean'],
            'status' => ['sometimes', 'string', 'in:'.implode(',', array_map(fn ($case) => $case->value, AssessmentResultStatus::cases()))],
            'remarks' => ['nullable', 'string'],
        ];
    }

    public static function create(): array
    {
        return self::baseRules();
    }

    public static function update(?int $resultId = null): array
    {
        $rules = self::baseRules();

        foreach ($rules as $key => $constraints) {
            $rules[$key] = array_merge(['sometimes'], $constraints);
        }

        $rules['raw_score'][] = 'prohibited_if:is_absent,true';

        return $rules;
    }

    public static function validate(array $data, string $mode = 'create', ?int $resultId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($resultId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
