<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Domain\Rules;

use App\Domains\Guardian\Domain\Enums\RelationshipFlag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Validation vocabulary for the student–guardian relationship aggregate.
 */
final class RelationshipRules
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public static function create(): array
    {
        return [
            'school_id' => ['required', 'string', 'exists:tenants,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'guardian_id' => ['required', 'integer', 'exists:guardians,id'],
            'relationship_type_id' => ['required', 'integer'],
            ...self::flagRules(),
            'emergency_contact_priority' => ['nullable', 'integer', 'min:1', 'max:255'],
            'pickup_valid_from' => ['nullable', 'date'],
            'pickup_valid_until' => ['nullable', 'date'],
            'relationship_start_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function validate(array $data): array
    {
        $validator = Validator::make($data, self::create());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * @return array<string, array<int, string>>
     */
    private static function flagRules(): array
    {
        $rules = [];

        foreach (RelationshipFlag::cases() as $flag) {
            $rules[$flag->column()] = ['boolean'];
        }

        return $rules;
    }
}
