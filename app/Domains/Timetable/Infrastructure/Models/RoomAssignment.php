<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Models;

use App\Domains\Timetable\Domain\Enums\RoomAssignmentPurpose;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Durable record of a room being used by an entry on a given weekly position.
 * Created whenever a room is assigned; keeps room usage queryable without
 * forcing the engine to own rooms.
 */
class RoomAssignment extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'room_assignments';

    protected $fillable = [
        'uuid',
        'school_id',
        'timetable_id',
        'schedule_entry_id',
        'room_id',
        'day_of_week',
        'period_number',
        'starts_at',
        'ends_at',
        'purpose',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'period_number' => 'integer',
            'purpose' => RoomAssignmentPurpose::class,
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Timetable\RoomAssignmentFactory();
    }

    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(ScheduleEntry::class, 'schedule_entry_id');
    }
}
