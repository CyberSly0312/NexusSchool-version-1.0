<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Immutable audit entry for an early departure (SPR-008).
 */
class EarlyDeparture extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'early_departures';

    protected $fillable = [
        'uuid',
        'school_id',
        'attendance_record_id',
        'minutes',
        'departed_at',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'minutes' => 'integer',
            'departed_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Attendance\EarlyDepartureFactory();
    }

    public function record(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class, 'attendance_record_id');
    }
}
