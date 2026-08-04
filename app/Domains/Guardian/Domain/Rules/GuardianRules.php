<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Rules;

use App\Domains\Guardian\Domain\Enums\GuardianStatus;
use App\Domains\Guardian\Domain\Enums\PreferredContactMethod;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Validation vocabulary for the Guardian aggregate. Declared once here and
 * reused by the Filament forms and by the application actions' pre-checks.
 */
final class GuardianRules
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public static function create(?string $schoolId = null, int|string|null $ignoreGuardianId = null): array
    {
        $codes = self::relationshipTypeCodes();

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'preferred_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'relationship_type' => ['nullable', 'in:'.implode(',', $codes)],
            'email' => ['nullable', 'email', 'max:255', self::uniqueEmail($schoolId, $ignoreGuardianId)],
            'phone_number' => ['nullable', 'string', 'max:30', self::uniquePhone($schoolId, $ignoreGuardianId)],
            'alternate_phone' => ['nullable', 'string', 'max:30'],
            'occupation' => ['nullable', 'string', 'max:200'],
            'employer' => ['nullable', 'string', 'max:200'],
            'residential_address' => ['nullable', 'string'],
            'postal_address' => ['nullable', 'string'],
            'preferred_communication_language' => ['nullable', 'string', 'max:50'],
            'preferred_contact_method' => ['nullable', 'in:'.implode(',', array_column(PreferredContactMethod::cases(), 'value'))],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'in:'.implode(',', array_column(GuardianStatus::cases(), 'value'))],
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function update(string $schoolId, int|string $ignoreGuardianId): array
    {
        $codes = self::relationshipTypeCodes();

        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'preferred_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['sometimes', 'nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'relationship_type' => ['sometimes', 'nullable', 'in:'.implode(',', $codes)],
            'email' => ['nullable', 'email', 'max:255', self::uniqueEmail($schoolId, $ignoreGuardianId)],
            'phone_number' => ['nullable', 'string', 'max:30', self::uniquePhone($schoolId, $ignoreGuardianId)],
            'alternate_phone' => ['nullable', 'string', 'max:30'],
            'occupation' => ['nullable', 'string', 'max:200'],
            'employer' => ['nullable', 'string', 'max:200'],
            'residential_address' => ['nullable', 'string'],
            'postal_address' => ['nullable', 'string'],
            'preferred_communication_language' => ['nullable', 'string', 'max:50'],
            'preferred_contact_method' => ['nullable', 'in:'.implode(',', array_column(PreferredContactMethod::cases(), 'value'))],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'nullable', 'in:'.implode(',', array_column(GuardianStatus::cases(), 'value'))],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function validate(array $data, string $mode = 'create', string $schoolId = '', int|string|null $ignoreGuardianId = null): array
    {
        $rules = $mode === 'update' && $ignoreGuardianId !== null
            ? self::update($schoolId, $ignoreGuardianId)
            : self::create($schoolId, $ignoreGuardianId);

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    private static function uniqueEmail(?string $schoolId, int|string|null $ignoreGuardianId = null): mixed
    {
        return $schoolId !== null && $schoolId !== ''
            ? RuleUnique::email($schoolId, $ignoreGuardianId)
            : 'nullable';
    }

    private static function uniquePhone(?string $schoolId, int|string|null $ignoreGuardianId = null): mixed
    {
        return $schoolId !== null && $schoolId !== ''
            ? RuleUnique::phone($schoolId, $ignoreGuardianId)
            : 'nullable';
    }

    /**
     * @return list<string>
     */
    private static function relationshipTypeCodes(): array
    {
        return array_column(config('nexus-guardian.relationship_types', []), 'code');
    }
}
