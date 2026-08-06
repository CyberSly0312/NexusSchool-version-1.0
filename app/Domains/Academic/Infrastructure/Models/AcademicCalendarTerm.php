<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Models;

use App\Domains\Academic\Domain\Enums\CalendarTermStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicCalendarTerm extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Academic\AcademicCalendarTermFactory();
    }

    protected $fillable = [
        'school_id',
        'calendar_year_id',
        'name',
        'code',
        'starts_on',
        'ends_on',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'status' => CalendarTermStatus::class,
        ];
    }

    public function calendarYear(): BelongsTo
    {
        return $this->belongsTo(AcademicCalendarYear::class, 'calendar_year_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(AcademicCalendarEvent::class, 'calendar_term_id');
    }

    public function isOpen(): bool
    {
        return $this->status === CalendarTermStatus::Open;
    }

    public function isPlanned(): bool
    {
        return $this->status === CalendarTermStatus::Planned;
    }

    public function isClosed(): bool
    {
        return $this->status === CalendarTermStatus::Closed;
    }
}
