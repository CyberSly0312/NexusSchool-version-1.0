<?php

declare(strict_types=1);

namespace App\Domains\Analytics\DTOs;

use App\Domains\Analytics\Enums\ReportFormat;
use App\Domains\Analytics\Enums\ReportStatus;
use App\Domains\Analytics\Enums\ScheduleFrequency;
use Illuminate\Support\Str;

readonly class ReportData
{
    public function __construct(
        public ?string $id,
        public string $schoolId,
        public string $name,
        public string $slug = '',
        public ?string $description = null,
        public string $type = 'custom',
        public string $format = 'pdf',
        public string $status = 'draft',
        public ?array $config = null,
        public ?string $scheduledAt = null,
        public ?string $scheduledFrequency = null,
        public ?string $lastGeneratedAt = null,
        public ?int $generatedBy = null,
    ) {
    }

    public static function create(array $data): self
    {
        $format = $data['format'] ?? 'pdf';
        if ($format instanceof ReportFormat) {
            $format = $format->value;
        }

        $status = $data['status'] ?? 'draft';
        if ($status instanceof ReportStatus) {
            $status = $status->value;
        }

        $scheduledFrequency = $data['scheduled_frequency'] ?? null;
        if ($scheduledFrequency instanceof ScheduleFrequency) {
            $scheduledFrequency = $scheduledFrequency->value;
        }

        return new self(
            id: $data['id'] ?? null,
            schoolId: (string) $data['school_id'],
            name: $data['name'],
            slug: $data['slug'] ?? Str::slug($data['name']),
            description: $data['description'] ?? null,
            type: $data['type'] ?? 'custom',
            format: $format,
            status: $status,
            config: $data['config'] ?? null,
            scheduledAt: $data['scheduled_at'] ?? null,
            scheduledFrequency: $scheduledFrequency,
            lastGeneratedAt: $data['last_generated_at'] ?? null,
            generatedBy: $data['generated_by'] ?? null,
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
            'type' => $this->type,
            'format' => $this->format instanceof ReportFormat
                ? $this->format->value : $this->format,
            'status' => $this->status instanceof ReportStatus
                ? $this->status->value : $this->status,
            'config' => $this->config,
            'scheduled_at' => $this->scheduledAt,
            'scheduled_frequency' => $this->scheduledFrequency instanceof ScheduleFrequency
                ? $this->scheduledFrequency->value : $this->scheduledFrequency,
            'last_generated_at' => $this->lastGeneratedAt,
            'generated_by' => $this->generatedBy,
        ];
    }
}
