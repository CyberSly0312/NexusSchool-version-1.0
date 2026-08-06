<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Models;

use App\Domains\Timetable\Domain\Enums\TimetableStatus;
use App\Models\User;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aggregate root of the Timetable domain (SPR-007).
 *
 * A weekly teaching grid for one academic period (year/term). The root only
 * references the Academic framework through opaque ids — it never owns years,
 * terms, classes, sections, subjects, teachers or rooms. Invariants (one
 * published timetable per period, immutable published snapshots) are enforced by
 * the application actions, never by the model.
 */
class Timetable extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'timetables';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_calendar_year_id',
        'academic_calendar_term_id',
        'name',
        'code',
        'description',
        'status',
        'version',
        'total_entries',
        'published_at',
        'archived_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => TimetableStatus::class,
            'version' => 'integer',
            'total_entries' => 'integer',
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Timetable\TimetableFactory();
    }

    public function isDraft(): bool
    {
        return $this->status === TimetableStatus::Draft;
    }

    public function isPublished(): bool
    {
        return $this->status === TimetableStatus::Published;
    }

    public function isArchived(): bool
    {
        return $this->status === TimetableStatus::Archived;
    }

    public function versions(): HasMany
    {
        return $this->hasMany(TimetableVersion::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ScheduleEntry::class);
    }

    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function conflicts(): HasMany
    {
        return $this->hasMany(TimetableConflict::class);
    }

    public function recurringPatterns(): HasMany
    {
        return $this->hasMany(RecurringPattern::class);
    }

    public function availabilityConstraints(): HasMany
    {
        return $this->hasMany(AvailabilityConstraint::class);
    }

    public function roomAssignments(): HasMany
    {
        return $this->hasMany(RoomAssignment::class);
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
