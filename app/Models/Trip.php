<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Transport\Enums\TripStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Trip extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'vehicle_id',
        'driver_id',
        'route_id',
        'direction',
        'academic_date',
        'departure_time',
        'arrival_time',
        'actual_departure',
        'actual_arrival',
        'status',
        'notes',
    ];

    protected $casts = [
        'academic_date' => 'date',
        'departure_time' => 'string',
        'arrival_time' => 'string',
        'actual_departure' => 'datetime',
        'actual_arrival' => 'datetime',
        'status' => TripStatus::class,
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
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDirectionLabelAttribute(): string
    {
        return $this->direction === 'to_school' ? 'To School' : 'From School';
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

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }
}
