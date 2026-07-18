<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Transport\Enums\MaintenanceStatus;
use App\Domains\Transport\Enums\MaintenanceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class VehicleMaintenance extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'vehicle_id',
        'type',
        'description',
        'cost',
        'service_provider',
        'service_date',
        'next_service_date',
        'odometer_reading',
        'receipt_document',
        'status',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'service_date' => 'date',
        'next_service_date' => 'date',
        'odometer_reading' => 'integer',
        'completed_at' => 'datetime',
        'type' => MaintenanceType::class,
        'status' => MaintenanceStatus::class,
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

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
