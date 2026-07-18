<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Transport\Enums\RouteStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Route extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'name',
        'description',
        'distance_km',
        'estimated_duration_minutes',
        'fare',
        'status',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'estimated_duration_minutes' => 'integer',
        'fare' => 'decimal:2',
        'status' => RouteStatus::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'school_id');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(RouteStop::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function studentAssignments(): HasMany
    {
        return $this->hasMany(StudentTransportAssignment::class);
    }
}
