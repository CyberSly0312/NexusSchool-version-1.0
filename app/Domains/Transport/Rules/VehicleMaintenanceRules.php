<?php

declare(strict_types=1);

namespace App\Domains\Transport\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class VehicleMaintenanceRules
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
            'type' => [$required, 'string', 'in:preventive,corrective,emergency,inspection'],
            'description' => [$required, 'string'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'service_provider' => ['nullable', 'string', 'max:255'],
            'service_date' => [$required, 'date'],
            'next_service_date' => ['nullable', 'date', 'after:service_date'],
            'odometer_reading' => ['nullable', 'integer', 'min:0'],
            'receipt_document' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:scheduled,in_progress,completed,cancelled'],
            'completed_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
