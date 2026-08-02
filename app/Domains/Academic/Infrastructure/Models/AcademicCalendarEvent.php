<?php

declare(strict_types=1);

namespace App\Domains\Academic\Infrastructure\Models;

use App\Domains\Academic\Domain\Enums\CalendarEventType;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicCalendarEvent extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'calendar_year_id',
        'calendar_term_id',
        'title',
        'type',
        'starts_on',
        'ends_on',
        'is_all_day',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'is_all_day' => 'boolean',
            'type' => CalendarEventType::class,
        ];
    }

    public function calendarYear(): BelongsTo
    {
        return $this->belongsTo(AcademicCalendarYear::class, 'calendar_year_id');
    }

    public function calendarTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicCalendarTerm::class, 'calendar_term_id');
    }
}
