<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class AutomationRule extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'name',
        'description',
        'category',
        'is_active',
        'priority',
        'trigger_type',
        'event_name',
        'conditions',
        'actions',
        'execution_count',
        'success_count',
        'failure_count',
        'last_executed_at',
        'cooldown_seconds',
        'created_by',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (AutomationRule $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'priority' => 'integer',
            'conditions' => 'array',
            'actions' => 'array',
            'execution_count' => 'integer',
            'success_count' => 'integer',
            'failure_count' => 'integer',
            'last_executed_at' => 'datetime',
            'cooldown_seconds' => 'integer',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByTriggerType($query, string $type)
    {
        return $query->where('trigger_type', $type);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}
