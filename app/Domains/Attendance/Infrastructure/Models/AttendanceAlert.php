<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Infrastructure\Models;

use App\Domains\Attendance\Domain\Enums\AttendanceAlertStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * An alert raised by the attendance alert engine (SPR-008).
 *
 * The engine only persists the alert and publishes domain events; notification
 * delivery is owned by the future Notifications domain.
 */
class AttendanceAlert extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'attendance_alerts';

    protected $fillable = [
        'uuid',
        'school_id',
        'enrollment_id',
        'attendance_session_id',
        'alert_type',
        'severity',
        'threshold',
        'observed_value',
        'payload',
        'detected_at',
        'status',
        'acknowledged_by',
        'acknowledged_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AttendanceAlertStatus::class,
            'threshold' => 'float',
            'observed_value' => 'float',
            'payload' => 'array',
            'detected_at' => 'datetime',
            'acknowledged_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Attendance\AttendanceAlertFactory();
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Enrollment\Infrastructure\Models\Enrollment::class, 'enrollment_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function isOpen(): bool
    {
        return $this->status === AttendanceAlertStatus::Open;
    }
}
