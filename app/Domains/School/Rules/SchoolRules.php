<?php

namespace App\Domains\School\Rules;

use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Enums\SchoolType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SchoolRules
{
    public static function create(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:schools,slug'],
            'database' => ['nullable', 'string', 'max:255', 'unique:schools,database'],
            'domain' => ['nullable', 'string', 'max:255', 'unique:schools,domain'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'currency' => ['nullable', 'string', 'size:3'],
            'locale' => ['nullable', 'string', 'max:10'],
            'logo_url' => ['nullable', 'url', 'max:2048'],
            'registration_number' => ['nullable', 'string', 'max:100', 'unique:schools,registration_number'],
            'school_type' => ['nullable', Rule::enum(SchoolType::class)],
            'status' => ['nullable', Rule::enum(SchoolStatus::class)],
            'settings' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public static function update(?string $schoolId = null): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('schools', 'slug')->ignore($schoolId)],
            'database' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('schools', 'database')->ignore($schoolId)],
            'domain' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('schools', 'domain')->ignore($schoolId)],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'address' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'city' => ['sometimes', 'nullable', 'string', 'max:100'],
            'state' => ['sometimes', 'nullable', 'string', 'max:100'],
            'country' => ['sometimes', 'nullable', 'string', 'max:100'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:20'],
            'timezone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'currency' => ['sometimes', 'nullable', 'string', 'size:3'],
            'locale' => ['sometimes', 'nullable', 'string', 'max:10'],
            'logo_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'registration_number' => ['sometimes', 'nullable', 'string', 'max:100', Rule::unique('schools', 'registration_number')->ignore($schoolId)],
            'school_type' => ['sometimes', 'nullable', Rule::enum(SchoolType::class)],
            'status' => ['sometimes', 'nullable', Rule::enum(SchoolStatus::class)],
            'settings' => ['sometimes', 'nullable', 'array'],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ];
    }

    public static function validate(array $data, string $mode = 'create', ?string $schoolId = null): array
    {
        $rules = match ($mode) {
            'update' => self::update($schoolId),
            default => self::create(),
        };

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
