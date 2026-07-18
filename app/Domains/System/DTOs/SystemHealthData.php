<?php

declare(strict_types=1);

namespace App\Domains\System\DTOs;

readonly class SystemHealthData
{
    public function __construct(
        public ?string $id,
        public string $service,
        public string $status,
        public ?int $responseTimeMs,
        public ?string $message,
        public ?array $metadata,
        public ?int $cpuUsagePercent,
        public ?int $memoryUsagePercent,
        public ?int $diskUsedBytes,
        public ?int $diskTotalBytes,
        public string $checkedAt,
    ) {
    }

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            service: $data['service'],
            status: $data['status'] ?? 'unknown',
            responseTimeMs: $data['response_time_ms'] ?? null,
            message: $data['message'] ?? null,
            metadata: $data['metadata'] ?? null,
            cpuUsagePercent: $data['cpu_usage_percent'] ?? null,
            memoryUsagePercent: $data['memory_usage_percent'] ?? null,
            diskUsedBytes: $data['disk_used_bytes'] ?? null,
            diskTotalBytes: $data['disk_total_bytes'] ?? null,
            checkedAt: $data['checked_at'] ?? now()->toIso8601String(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'service' => $this->service,
            'status' => $this->status,
            'response_time_ms' => $this->responseTimeMs,
            'message' => $this->message,
            'metadata' => $this->metadata,
            'cpu_usage_percent' => $this->cpuUsagePercent,
            'memory_usage_percent' => $this->memoryUsagePercent,
            'disk_used_bytes' => $this->diskUsedBytes,
            'disk_total_bytes' => $this->diskTotalBytes,
            'checked_at' => $this->checkedAt,
        ];
    }
}
