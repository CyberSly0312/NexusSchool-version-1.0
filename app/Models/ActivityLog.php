<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'activity_logs';

    protected $fillable = [
        'school_id',
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'event',
        'properties',
        'ip_address',
        'user_agent',
        'causer_id',
        'causer_type',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (ActivityLog $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function subject(): \Illuminate\Database\Eloquent\MorphTo
    {
        return $this->morphTo();
    }

    public function causer(): \Illuminate\Database\Eloquent\MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForSchool($query, ?string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByLogName($query, string $logName)
    {
        return $query->where('log_name', $logName);
    }

    public function scopeByEvent($query, string $event)
    {
        return $query->where('event', $event);
    }
}
