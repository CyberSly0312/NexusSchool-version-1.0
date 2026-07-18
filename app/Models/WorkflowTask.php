<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class WorkflowTask extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'workflow_execution_id',
        'node_id',
        'type',
        'name',
        'status',
        'order',
        'assigned_to',
        'assigned_to_type',
        'assigned_by',
        'assigned_at',
        'input_data',
        'output_data',
        'notes',
        'error',
        'started_at',
        'completed_at',
        'due_at',
        'duration_ms',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (WorkflowTask $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'input_data' => 'array',
            'output_data' => 'array',
            'assigned_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'due_at' => 'datetime',
            'duration_ms' => 'integer',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function execution(): BelongsTo
    {
        return $this->belongsTo(WorkflowExecution::class, 'workflow_execution_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeForAssignee($query, string $assignee)
    {
        return $query->where('assigned_to', $assignee);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_at', '<', now())->whereIn('status', ['pending', 'assigned', 'in_progress']);
    }
}
