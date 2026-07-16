<?php

namespace App\Domains\Guardian\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GuardianRules
{
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'guardian_number' => ['required', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'preferred_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'relationship_type' => ['required', 'string', 'in:father,mother,legal_guardian,grandparent,uncle,aunt,brother,sister,foster_parent,other'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:30'],
            'alternate_phone' => ['nullable', 'string', 'max:30'],
            'occupation' => ['nullable', 'string', 'max:200'],
            'employer' => ['nullable', 'string', 'max:200'],
            'residential_address' => ['nullable', 'string'],
            'postal_address' => ['nullable', 'string'],
            'emergency_contact' => ['nullable', 'string'],
            'preferred_communication_language' => ['nullable', 'string', 'max:50'],
            'preferred_contact_method' => ['sometimes', 'string', 'in:phone,sms,email,whatsapp'],
            'is_primary_guardian' => ['sometimes', 'boolean'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function update(?int $guardianId = null): array
    {
        return [
            'school_id' => ['sometimes', 'required', 'string', 'exists:tenants,id'],
            'guardian_number' => ['sometimes', 'required', 'string', 'max:50'],
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'preferred_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['sometimes', 'required', 'string', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'relationship_type' => ['sometimes', 'required', 'string', 'in:father,mother,legal_guardian,grandparent,uncle,aunt,brother,sister,foster_parent,other'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['sometimes', 'required', 'string', 'max:30'],
            'alternate_phone' => ['nullable', 'string', 'max:30'],
            'occupation' => ['nullable', 'string', 'max:200'],
            'employer' => ['nullable', 'string', 'max:200'],
            'residential_address' => ['nullable', 'string'],
            'postal_address' => ['nullable', 'string'],
            'emergency_contact' => ['nullable', 'string'],
            'preferred_communication_language' => ['nullable', 'string', 'max:50'],
            'preferred_contact_method' => ['sometimes', 'string', 'in:phone,sms,email,whatsapp'],
            'is_primary_guardian' => ['sometimes', 'boolean'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?int $guardianId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($guardianId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
