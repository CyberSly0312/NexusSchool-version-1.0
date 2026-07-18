<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ScheduledTask extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'name',
        'command',
        'arguments',
        'cron_expression',
        'timezone',
        'is_active',
        'is_singleton',
        'status',
        'last_run_at',
        'next_run_at',
        'total_runs',
        'failed_runs',
        'last_error',
        'metadata',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (ScheduledTask $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_singleton' => 'boolean',
            'total_runs' => 'integer',
            'failed_runs' => 'integer',
            'last_run_at' => 'datetime',
            'next_run_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueNow($query)
    {
        return $query->active()->where(function ($q) {
            $q->whereNull('next_run_at')->orWhere('next_run_at', '<=', now());
        });
    }
}
