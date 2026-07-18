<?php

declare(strict_types=1);

namespace App\Domains\Transport\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class VehicleRules
{
    public static function validate(array $data, string $mode = 'create'): array
    {
        $rules = self::rules($mode);
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $validator->validated();
    }

    public static function rules(string $mode = 'create'): array
    {
        $required = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'school_id' => [$required, 'string'],
            'registration_number' => [$required, 'string', 'max:255'],
            'vin' => [$required, 'string', 'size:17'],
            'manufacturer' => [$required, 'string', 'max:100'],
            'model' => [$required, 'string', 'max:100'],
            'year' => [$required, 'integer', 'min:1900', 'max:2030'],
            'color' => ['nullable', 'string', 'max:50'],
            'capacity' => [$required, 'integer', 'min:1'],
            'fuel_type' => [$required, 'string', 'max:50'],
            'insurance_expiry' => ['nullable', 'date'],
            'inspection_expiry' => ['nullable', 'date'],
            'insurance_document' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:available,assigned,in_service,maintenance,retired'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
