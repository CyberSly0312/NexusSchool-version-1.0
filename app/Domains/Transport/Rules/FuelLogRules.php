<?php

declare(strict_types=1);

namespace App\Domains\Transport\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FuelLogRules
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
            'vehicle_id' => [$required, 'string'],
            'driver_id' => ['nullable', 'string'],
            'liters' => [$required, 'numeric', 'min:0.01'],
            'cost_per_liter' => [$required, 'numeric', 'min:0'],
            'total_cost' => [$required, 'numeric', 'min:0'],
            'fuel_type' => [$required, 'string', 'max:50'],
            'fuel_station' => ['nullable', 'string', 'max:255'],
            'odometer_reading' => [$required, 'integer', 'min:0'],
            'fuel_date' => [$required, 'date'],
            'receipt_document' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
