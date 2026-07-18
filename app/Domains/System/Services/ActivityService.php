<?php

declare(strict_types=1);

namespace App\Domains\System\Services;

use App\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityService
{
    public function __construct(
        protected ActivityLog $model = new ActivityLog(),
    ) {
    }

    public function record(array $data): ActivityLog
    {
        return $this->model->create([
            'school_id' => $data['school_id'] ?? null,
            'log_name' => $data['log_name'] ?? 'default',
            'description' => $data['description'],
            'subject_type' => $data['subject_type'] ?? null,
            'subject_id' => $data['subject_id'] ?? null,
            'event' => $data['event'] ?? null,
            'properties' => $data['properties'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'causer_id' => $data['causer_id'] ?? null,
            'causer_type' => $data['causer_type'] ?? null,
        ]);
    }

    public function getForModel(string $type, string $id): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model
            ->where('subject_type', $type)
            ->where('subject_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['log_name'])) {
            $query->where('log_name', $filters['log_name']);
        }

        if (isset($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
