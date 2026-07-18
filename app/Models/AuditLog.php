<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'audit_logs';

    protected $fillable = [
        'school_id',
        'auditable_type',
        'auditable_id',
        'action',
        'old_values',
        'new_values',
        'event',
        'description',
        'ip_address',
        'user_agent',
        'tags',
        'user_id',
        'user_type',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (AuditLog $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function auditable(): \Illuminate\Database\Eloquent\MorphTo
    {
        return $this->morphTo();
    }

    public function user(): \Illuminate\Database\Eloquent\MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForSchool($query, ?string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('auditable_type', $type);
    }
}
