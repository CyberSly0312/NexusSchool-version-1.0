<?php

declare(strict_types=1);

namespace App\Domains\Student\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StudentRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'student_number' => ['required', 'string', 'max:50'],
            'admission_number' => ['nullable', 'string', 'max:50'],
            'national_id' => ['nullable', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'preferred_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'place_of_birth' => ['nullable', 'string', 'max:200'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'blood_group' => ['nullable', 'string', 'max:10', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'religion' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'residential_address' => ['nullable', 'string'],
            'admission_date' => ['nullable', 'date'],
            'allergies' => ['nullable', 'string'],
            'medical_notes' => ['nullable', 'string'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:active,inactive,graduated,suspended,withdrawn,archived'],
        ];
    }

    public static function update(?int $studentId = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'student_number' => ['sometimes', 'required', 'string', 'max:50'],
            'admission_number' => ['nullable', 'string', 'max:50'],
            'national_id' => ['nullable', 'string', 'max:100'],
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'preferred_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['sometimes', 'required', 'string', 'in:male,female,other'],
            'date_of_birth' => ['sometimes', 'required', 'date', 'before:today'],
            'place_of_birth' => ['nullable', 'string', 'max:200'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'blood_group' => ['nullable', 'string', 'max:10', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'religion' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'residential_address' => ['nullable', 'string'],
            'admission_date' => ['nullable', 'date'],
            'allergies' => ['nullable', 'string'],
            'medical_notes' => ['nullable', 'string'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:active,inactive,graduated,suspended,withdrawn,archived'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $studentId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($studentId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
