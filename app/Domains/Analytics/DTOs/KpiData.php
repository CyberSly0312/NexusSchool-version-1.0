<?php

declare(strict_types=1);

namespace App\Domains\Analytics\DTOs;

use App\Domains\Analytics\Enums\KpiStatus;
use App\Domains\Analytics\Enums\ScheduleFrequency;
use Illuminate\Support\Str;

readonly class KpiData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $name,
        public string $slug = '',
        public ?string $description = null,
        public string $category = 'general',
        public ?array $formula = null,
        public ?float $targetValue = null,
        public ?string $unit = null,
        public string $status = 'active',
        public ?string $calculationFrequency = null,
        public ?string $lastCalculatedAt = null,
        public ?float $cachedValue = null,
        public ?string $cachedAt = null,
    ) {
    }

    public static function create(array $data): self
    {
        $status = $data['status'] ?? 'active';
        if ($status instanceof KpiStatus) {
            $status = $status->value;
        }

        $calculationFrequency = $data['calculation_frequency'] ?? null;
        if ($calculationFrequency instanceof ScheduleFrequency) {
            $calculationFrequency = $calculationFrequency->value;
        }

        return new self(
            id: $data['id'] ?? null,
            schoolId: (string) $data['school_id'],
            name: $data['name'],
            slug: $data['slug'] ?? Str::slug($data['name']),
            description: $data['description'] ?? null,
            category: $data['category'] ?? 'general',
            formula: $data['formula'] ?? null,
            targetValue: isset($data['target_value']) ? (float) $data['target_value'] : null,
            unit: $data['unit'] ?? null,
            status: $status,
            calculationFrequency: $calculationFrequency,
            lastCalculatedAt: $data['last_calculated_at'] ?? null,
            cachedValue: isset($data['cached_value']) ? (float) $data['cached_value'] : null,
            cachedAt: $data['cached_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->schoolId,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'category' => $this->category,
            'formula' => $this->formula,
            'target_value' => $this->targetValue,
            'unit' => $this->unit,
            'status' => $this->status instanceof KpiStatus
                ? $this->status->value : $this->status,
            'calculation_frequency' => $this->calculationFrequency instanceof ScheduleFrequency
                ? $this->calculationFrequency->value : $this->calculationFrequency,
            'last_calculated_at' => $this->lastCalculatedAt,
            'cached_value' => $this->cachedValue,
            'cached_at' => $this->cachedAt,
        ];
    }
}
