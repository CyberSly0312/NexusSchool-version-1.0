<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class WorkflowSchedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'workflow_id',
        'trigger_id',
        'name',
        'cron_expression',
        'timezone',
        'is_active',
        'payload',
        'conditions',
        'last_run_at',
        'next_run_at',
        'total_runs',
        'failed_runs',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (WorkflowSchedule $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'payload' => 'array',
            'conditions' => 'array',
            'last_run_at' => 'datetime',
            'next_run_at' => 'datetime',
            'total_runs' => 'integer',
            'failed_runs' => 'integer',
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

    public function trigger(): BelongsTo
    {
        return $this->belongsTo(WorkflowTrigger::class, 'trigger_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueNow($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('next_run_at')
                    ->orWhere('next_run_at', '<=', now());
            });
    }
}
