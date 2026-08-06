<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Infrastructure\Models;

use App\Domains\Attendance\Domain\Enums\AttendanceSessionStatus;
use App\Domains\Attendance\Domain\Exceptions\InvalidStatusTransitionException;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aggregate root of the Attendance engine (SPR-008).
 *
 * A session is one class + one time slot on one date. Invariants are enforced
 * by the application actions, never by the model itself: one session per class
 * and time slot, a forward-only lifecycle (Draft → Open → Taking Attendance →
 * Submitted → Locked → Archived), and full immutability once locked.
 */
class AttendanceSession extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'attendance_sessions';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_calendar_year_id',
        'academic_calendar_term_id',
        'school_class_id',
        'class_section_id',
        'academic_subject_id',
        'timetable_id',
        'schedule_entry_id',
        'date',
        'day_of_week',
        'period_number',
        'starts_at',
        'ends_at',
        'staff_id',
        'status',
        'expected_students_count',
        'recorded_students_count',
        'opened_at',
        'submitted_at',
        'locked_at',
        'archived_at',
        'created_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => AttendanceSessionStatus::class,
            'date' => 'date',
            'day_of_week' => 'integer',
            'period_number' => 'integer',
            'expected_students_count' => 'integer',
            'recorded_students_count' => 'integer',
            'opened_at' => 'datetime',
            'submitted_at' => 'datetime',
            'locked_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Attendance\AttendanceSessionFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function records(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'attendance_session_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(AttendanceAlert::class, 'attendance_session_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Lifecycle helpers
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === AttendanceSessionStatus::Draft;
    }

    public function isOpen(): bool
    {
        return $this->status === AttendanceSessionStatus::Open;
    }

    public function isTakingAttendance(): bool
    {
        return $this->status === AttendanceSessionStatus::TakingAttendance;
    }

    public function isSubmitted(): bool
    {
        return $this->status === AttendanceSessionStatus::Submitted;
    }

    public function isLocked(): bool
    {
        return $this->status === AttendanceSessionStatus::Locked;
    }

    public function isArchived(): bool
    {
        return $this->status === AttendanceSessionStatus::Archived;
    }

    public function isRecordable(): bool
    {
        return $this->status->isRecordable();
    }

    public function isImmutable(): bool
    {
        return $this->status->isImmutable();
    }

    public function canTransitionTo(AttendanceSessionStatus $to): bool
    {
        return $to->allowsTransitionFrom($this->status);
    }

    /**
     * @throws InvalidStatusTransitionException
     */
    public function assertCanTransitionTo(AttendanceSessionStatus $to): void
    {
        if (! $this->canTransitionTo($to)) {
            throw InvalidStatusTransitionException::from($this->status, $to);
        }
    }

    /**
     * @throws \App\Domains\Attendance\Domain\Exceptions\SessionLockedException
     */
    public function assertMutable(): void
    {
        if ($this->isImmutable()) {
            throw \App\Domains\Attendance\Domain\Exceptions\SessionLockedException::forSession($this->getKey());
        }
    }

    public function displayName(): string
    {
        return sprintf(
            'Classe #%s — %s (P%d)',
            $this->school_class_id,
            $this->date?->toDateString() ?? '?',
            $this->period_number,
        );
    }
}
