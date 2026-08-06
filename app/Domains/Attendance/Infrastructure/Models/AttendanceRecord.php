<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Infrastructure\Models;

use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A single student's attendance in a session (SPR-008).
 *
 * Bound to an enrollment, never to a student directly. One record per
 * enrollment per session (unique constraint); late arrivals and early
 * departures are supporting entities kept as immutable audit entries.
 */
class AttendanceRecord extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'attendance_session_records';

    protected $fillable = [
        'uuid',
        'school_id',
        'attendance_session_id',
        'enrollment_id',
        'status',
        'check_in_time',
        'check_out_time',
        'late_minutes',
        'recorded_at',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => AttendanceStatus::class,
            'check_in_time' => 'datetime',
            'check_out_time' => 'datetime',
            'late_minutes' => 'integer',
            'recorded_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Attendance\AttendanceRecordFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function corrections(): HasMany
    {
        return $this->hasMany(AttendanceCorrection::class, 'attendance_record_id');
    }

    public function lateArrival(): HasOne
    {
        return $this->hasOne(LateArrival::class, 'attendance_record_id');
    }

    public function earlyDeparture(): HasOne
    {
        return $this->hasOne(EarlyDeparture::class, 'attendance_record_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isLate(): bool
    {
        return $this->status === AttendanceStatus::Late;
    }

    public function isAbsent(): bool
    {
        return $this->status === AttendanceStatus::Absent || $this->status === AttendanceStatus::Unexcused;
    }
}
