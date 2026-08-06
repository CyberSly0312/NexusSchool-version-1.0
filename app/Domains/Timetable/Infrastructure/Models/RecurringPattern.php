<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Models;

use App\Domains\Timetable\Domain\Enums\RecurrenceFrequency;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Recurrence definition attached to a timetable, a slot or an entry. Drives the
 * expansion of a weekly slot into concrete lesson dates.
 */
class RecurringPattern extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'recurring_patterns';

    protected $fillable = [
        'uuid',
        'school_id',
        'timetable_id',
        'schedule_entry_id',
        'time_slot_id',
        'frequency',
        'interval',
        'occurs_on_days',
        'starts_on',
        'ends_on',
        'end_after_occurrences',
        'exceptions',
    ];

    protected function casts(): array
    {
        return [
            'frequency' => RecurrenceFrequency::class,
            'interval' => 'integer',
            'occurs_on_days' => 'array',
            'exceptions' => 'array',
            'starts_on' => 'date',
            'ends_on' => 'date',
            'end_after_occurrences' => 'integer',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Timetable\RecurringPatternFactory();
    }

    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(ScheduleEntry::class, 'schedule_entry_id');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
