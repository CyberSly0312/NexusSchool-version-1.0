<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class WorkflowExecution extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'workflow_id',
        'workflow_version_id',
        'trigger_id',
        'status',
        'current_node_id',
        'context',
        'variables',
        'result',
        'error',
        'started_at',
        'completed_at',
        'duration_ms',
        'retry_count',
        'max_retries',
        'idempotency_key',
        'initiated_by',
        'initiated_by_type',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (WorkflowExecution $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'status' => \App\Domains\Workflow\Enums\ExecutionStatus::class,
            'context' => 'array',
            'variables' => 'array',
            'result' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'duration_ms' => 'integer',
            'retry_count' => 'integer',
            'max_retries' => 'integer',
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

    public function version(): BelongsTo
    {
        return $this->belongsTo(WorkflowVersion::class, 'workflow_version_id');
    }

    public function trigger(): BelongsTo
    {
        return $this->belongsTo(WorkflowTrigger::class, 'trigger_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(WorkflowTask::class, 'workflow_execution_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeForWorkflow($query, string $workflowId)
    {
        return $query->where('workflow_id', $workflowId);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
