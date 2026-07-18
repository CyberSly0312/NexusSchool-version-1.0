<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class MaintenanceWindow extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'name',
        'description',
        'type',
        'status',
        'is_readonly',
        'starts_at',
        'ends_at',
        'actual_start_at',
        'actual_end_at',
        'affected_services',
        'metadata',
        'created_by',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (MaintenanceWindow $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_readonly' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'actual_start_at' => 'datetime',
            'actual_end_at' => 'datetime',
            'affected_services' => 'array',
            'metadata' => 'array',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && now()->between($this->starts_at, $this->ends_at);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForSchool($query, ?string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
