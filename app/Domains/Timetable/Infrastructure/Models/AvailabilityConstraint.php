<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Models;

use App\Domains\Timetable\Domain\Enums\AvailabilityConstraintType;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Blocks a resource (teacher, room or class) for a day, a time window or a date
 * window inside a timetable. The target is a generic (type, id) pair so the
 * engine never depends on the target's model.
 */
class AvailabilityConstraint extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'availability_constraints';

    protected $fillable = [
        'uuid',
        'school_id',
        'timetable_id',
        'time_slot_id',
        'constraint_type',
        'target_type',
        'target_id',
        'day_of_week',
        'starts_at',
        'ends_at',
        'starts_on',
        'ends_on',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'constraint_type' => AvailabilityConstraintType::class,
            'day_of_week' => 'integer',
            'starts_on' => 'date',
            'ends_on' => 'date',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Timetable\AvailabilityConstraintFactory();
    }

    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function blocksDay(int $dayOfWeek): bool
    {
        return $this->day_of_week === null || $this->day_of_week === $dayOfWeek;
    }

    /**
     * Whether the constraint blocks the given weekly window.
     */
    public function blocks(string $time, int $dayOfWeek, ?string $date = null): bool
    {
        if (! $this->blocksDay($dayOfWeek)) {
            return false;
        }

        if ($date !== null && $this->starts_on !== null && $date < $this->starts_on->toDateString()) {
            return false;
        }

        if ($date !== null && $this->ends_on !== null && $date > $this->ends_on->toDateString()) {
            return false;
        }

        if ($this->starts_at === null || $this->ends_at === null) {
            return true;
        }

        return $time >= $this->starts_at && $time < $this->ends_at;
    }
}
