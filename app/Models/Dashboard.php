<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Analytics\Enums\DashboardType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\School\Models\School;
use Illuminate\Support\Str;

class Dashboard extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id', 'name', 'slug', 'description', 'type',
        'is_default', 'is_active', 'layout', 'created_by',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Dashboard $model) {
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
            'type' => DashboardType::class,
            'layout' => 'array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(DashboardWidget::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
