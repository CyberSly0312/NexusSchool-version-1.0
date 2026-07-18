<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class WorkflowVersion extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'workflow_id',
        'version',
        'status',
        'changelog',
        'nodes',
        'edges',
        'settings',
        'published_by',
        'published_at',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (WorkflowVersion $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'nodes' => 'array',
            'edges' => 'array',
            'settings' => 'array',
            'published_at' => 'datetime',
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

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeForWorkflow($query, string $workflowId)
    {
        return $query->where('workflow_id', $workflowId);
    }
}
