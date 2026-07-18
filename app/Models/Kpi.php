<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Analytics\Enums\KpiStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class Kpi extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id', 'name', 'slug', 'description', 'category',
        'formula', 'target_value', 'unit', 'status',
        'calculation_frequency', 'last_calculated_at',
        'cached_value', 'cached_at',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Kpi $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'status' => KpiStatus::class,
            'formula' => 'array',
            'target_value' => 'decimal:2',
            'cached_value' => 'decimal:2',
            'last_calculated_at' => 'datetime',
            'cached_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', KpiStatus::Active);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function isCacheValid(): bool
    {
        return $this->cached_value !== null
            && $this->cached_at !== null
            && $this->cached_at->diffInMinutes(now()) < 60;
    }
}
