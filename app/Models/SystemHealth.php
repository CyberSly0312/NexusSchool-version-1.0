<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SystemHealth extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'system_health';

    protected $fillable = [
        'service',
        'status',
        'response_time_ms',
        'message',
        'metadata',
        'cpu_usage_percent',
        'memory_usage_percent',
        'disk_used_bytes',
        'disk_total_bytes',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (SystemHealth $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'response_time_ms' => 'integer',
            'metadata' => 'array',
            'cpu_usage_percent' => 'integer',
            'memory_usage_percent' => 'integer',
            'disk_used_bytes' => 'integer',
            'disk_total_bytes' => 'integer',
        ];
    }

    public function scopeForService($query, string $service)
    {
        return $query->where('service', $service);
    }

    public function scopeHealthy($query)
    {
        return $query->where('status', 'healthy');
    }

    public function scopeUnhealthy($query)
    {
        return $query->where('status', 'unhealthy');
    }
}
