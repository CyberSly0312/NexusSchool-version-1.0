<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Infrastructure\Models;

use App\Domains\Attendance\Domain\ValueObjects\AttendancePercentage;
use App\Domains\Attendance\Domain\ValueObjects\RiskScore;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Materialized attendance statistics for one enrollment over one period
 * (SPR-008). Produced by GenerateAttendanceSummaryAction; kept so reports never
 * re-derive heavy aggregations.
 */
class AttendanceSummary extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'attendance_summaries';

    protected $fillable = [
        'uuid',
        'school_id',
        'enrollment_id',
        'period_type',
        'period_start',
        'period_end',
        'sessions_count',
        'attended_count',
        'present_count',
        'late_count',
        'excused_count',
        'unexcused_count',
        'absent_count',
        'attendance_percentage',
        'late_percentage',
        'consecutive_absences',
        'consecutive_lates',
        'risk_score',
        'trend',
        'breakdown',
        'calculated_at',
        'calculated_by',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'sessions_count' => 'integer',
            'attended_count' => 'integer',
            'present_count' => 'integer',
            'late_count' => 'integer',
            'excused_count' => 'integer',
            'unexcused_count' => 'integer',
            'absent_count' => 'integer',
            'attendance_percentage' => 'float',
            'late_percentage' => 'float',
            'consecutive_absences' => 'integer',
            'consecutive_lates' => 'integer',
            'risk_score' => 'float',
            'trend' => 'float',
            'breakdown' => 'array',
            'calculated_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Attendance\AttendanceSummaryFactory();
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Enrollment\Infrastructure\Models\Enrollment::class, 'enrollment_id');
    }

    public function attendancePercentage(): AttendancePercentage
    {
        return AttendancePercentage::fromFloat($this->attendance_percentage);
    }

    public function riskScore(): RiskScore
    {
        return new RiskScore($this->risk_score);
    }
}
