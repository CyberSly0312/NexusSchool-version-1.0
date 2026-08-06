<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A recurring position inside a Timetable's weekly grid. Entries attach to a
 * TimeSlot; the slot is valid for a date window (a whole term by default) so a
 * single slot can be expanded into concrete lesson dates by the recurrence
 * engine.
 */
class TimeSlot extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'time_slots';

    protected $fillable = [
        'uuid',
        'school_id',
        'timetable_id',
        'teaching_period_id',
        'day_of_week',
        'period_number',
        'starts_at',
        'ends_at',
        'week_start',
        'week_end',
        'label',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'period_number' => 'integer',
            'week_start' => 'date',
            'week_end' => 'date',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Timetable\TimeSlotFactory();
    }

    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    public function teachingPeriod(): BelongsTo
    {
        return $this->belongsTo(TeachingPeriod::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ScheduleEntry::class);
    }
}
