<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Infrastructure\Models;

use App\Contracts\SchoolOwned;
use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Authorized correction of a single attendance record (SPR-008).
 *
 * Corrections are append-only audit entries: they never overwrite the record's
 * history, and they require explicit authorization before they take effect.
 */
class AttendanceCorrection extends Model implements SchoolOwned
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'attendance_corrections';

    protected $fillable = [
        'uuid',
        'school_id',
        'attendance_record_id',
        'previous_status',
        'new_status',
        'reason_type',
        'reason_text',
        'is_authorized',
        'requested_by',
        'authorized_by',
        'authorized_at',
        'corrected_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'previous_status' => AttendanceStatus::class,
            'new_status' => AttendanceStatus::class,
            'is_authorized' => 'boolean',
            'authorized_at' => 'datetime',
            'corrected_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Attendance\AttendanceCorrectionFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function record(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class, 'attendance_record_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isAuthorized(): bool
    {
        return $this->is_authorized;
    }
}
