<?php

declare(strict_types=1);

namespace App\Domains\System\Services;

use App\Domains\System\DTOs\AuditLogData;
use App\Domains\System\Events\AuditRecorded;
use App\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditService
{
    public function __construct(
        protected AuditLog $model = new AuditLog(),
    ) {
    }

    public function record(array $data): AuditLogData
    {
        $auditLog = $this->model->create([
            'school_id' => $data['school_id'] ?? null,
            'auditable_type' => $data['auditable_type'],
            'auditable_id' => $data['auditable_id'],
            'action' => $data['action'],
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'event' => $data['event'] ?? null,
            'description' => $data['description'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'tags' => $data['tags'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'user_type' => $data['user_type'] ?? null,
        ]);

        AuditRecorded::dispatch($auditLog);

        return AuditLogData::create($auditLog->toArray());
    }

    public function findById(string $id): AuditLog
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['auditable_type'])) {
            $query->where('auditable_type', $filters['auditable_type']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('event', 'like', "%{$search}%");
            });
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getForModel(string $type, string $id): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model
            ->where('auditable_type', $type)
            ->where('auditable_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }
}
