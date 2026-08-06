<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Models;

use App\Domains\Timetable\Domain\Enums\ScheduleEntryStatus;
use App\Domains\Timetable\Domain\Enums\ScheduleEntryType;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One teaching event in a Timetable: a class, on a day, in a period, for a
 * subject, with an optional teacher and room. Kept self-contained (day, period,
 * times are denormalized) so entries survive slot refactoring.
 */
class ScheduleEntry extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'schedule_entries';

    protected $fillable = [
        'uuid',
        'school_id',
        'timetable_id',
        'time_slot_id',
        'parent_entry_id',
        'academic_subject_id',
        'school_class_id',
        'class_section_id',
        'staff_id',
        'room_id',
        'day_of_week',
        'period_number',
        'starts_at',
        'ends_at',
        'duration_minutes',
        'entry_type',
        'status',
        'is_recurring',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'period_number' => 'integer',
            'duration_minutes' => 'integer',
            'entry_type' => ScheduleEntryType::class,
            'status' => ScheduleEntryStatus::class,
            'is_recurring' => 'boolean',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Timetable\ScheduleEntryFactory();
    }

    public function isScheduled(): bool
    {
        return $this->status === ScheduleEntryStatus::Scheduled;
    }

    public function isCancelled(): bool
    {
        return $this->status === ScheduleEntryStatus::Cancelled;
    }

    public function isRescheduled(): bool
    {
        return $this->status === ScheduleEntryStatus::Rescheduled;
    }

    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function parentEntry(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_entry_id');
    }

    public function supersessions(): HasMany
    {
        return $this->hasMany(self::class, 'parent_entry_id');
    }

    public function roomAssignments(): HasMany
    {
        return $this->hasMany(RoomAssignment::class);
    }
}
