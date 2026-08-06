<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Models;

use App\Domains\Academic\Domain\Enums\CalendarYearStatus;
use App\Domains\School\Models\School;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicCalendarYear extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Academic\AcademicCalendarYearFactory();
    }

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'start_date',
        'end_date',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => CalendarYearStatus::class,
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function terms(): HasMany
    {
        return $this->hasMany(AcademicCalendarTerm::class, 'calendar_year_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(AcademicCalendarEvent::class, 'calendar_year_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', CalendarYearStatus::Active);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', CalendarYearStatus::Draft);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', CalendarYearStatus::Archived);
    }

    public function isActive(): bool
    {
        return $this->status === CalendarYearStatus::Active;
    }

    public function isDraft(): bool
    {
        return $this->status === CalendarYearStatus::Draft;
    }

    public function isArchived(): bool
    {
        return $this->status === CalendarYearStatus::Archived;
    }
}
