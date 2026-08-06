<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Models;

use App\Domains\Timetable\Domain\Enums\ConflictType;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One conflict discovered by the conflict engine during a detection run.
 * Rows are immutable evidence; resolution is tracked on the row itself so the
 * report history stays complete.
 */
class TimetableConflict extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'timetable_conflicts';

    protected $fillable = [
        'uuid',
        'school_id',
        'timetable_id',
        'detection_id',
        'conflict_type',
        'entry_id',
        'conflicting_entry_id',
        'target_type',
        'target_id',
        'day_of_week',
        'starts_at',
        'ends_at',
        'reason',
        'resolved_at',
        'resolution',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'conflict_type' => ConflictType::class,
            'day_of_week' => 'integer',
            'resolved_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Timetable\TimetableConflictFactory();
    }

    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }

    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(ScheduleEntry::class, 'entry_id');
    }

    public function conflictingEntry(): BelongsTo
    {
        return $this->belongsTo(ScheduleEntry::class, 'conflicting_entry_id');
    }
}
