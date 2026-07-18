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

class AiProvider extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'name',
        'slug',
        'driver',
        'description',
        'is_active',
        'configuration',
        'supported_models',
        'default_model',
        'rate_limit_per_minute',
        'daily_token_limit',
        'monthly_token_limit',
        'created_by',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (AiProvider $model) {
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
            'is_active' => 'boolean',
            'configuration' => 'array',
            'supported_models' => 'array',
            'rate_limit_per_minute' => 'integer',
            'daily_token_limit' => 'integer',
            'monthly_token_limit' => 'integer',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(AiConversation::class);
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(AiUsageLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByDriver($query, string $driver)
    {
        return $query->where('driver', $driver);
    }
}
