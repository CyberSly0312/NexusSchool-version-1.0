<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A fixed slot of the school day in the weekly grid (day + period + time).
 * Global to a school and reusable across timetables; seeded from configuration.
 */
class TeachingPeriod extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'teaching_periods';

    protected $fillable = [
        'uuid',
        'school_id',
        'day_of_week',
        'period_number',
        'starts_at',
        'ends_at',
        'duration_minutes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'period_number' => 'integer',
            'duration_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Timetable\TeachingPeriodFactory();
    }

    public function durationMinutes(): int
    {
        if ($this->duration_minutes !== null) {
            return $this->duration_minutes;
        }

        $start = strtotime($this->starts_at);
        $end = strtotime($this->ends_at);

        return (int) round(($end - $start) / 60);
    }
}
