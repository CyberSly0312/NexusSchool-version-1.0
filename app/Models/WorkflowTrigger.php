<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class WorkflowTrigger extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'workflow_id',
        'type',
        'name',
        'description',
        'is_active',
        'event_name',
        'conditions',
        'schedule',
        'metadata',
        'cooldown_seconds',
        'max_executions_per_hour',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (WorkflowTrigger $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'conditions' => 'array',
            'schedule' => 'array',
            'metadata' => 'array',
            'cooldown_seconds' => 'integer',
            'max_executions_per_hour' => 'integer',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByEvent($query, string $eventName)
    {
        return $query->where('event_name', $eventName);
    }
}
