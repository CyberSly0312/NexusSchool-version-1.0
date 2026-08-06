<?php

declare(strict_types=1);

namespace App\Domains\Timetable\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Append-only snapshot of a Timetable at a given lifecycle event. Created on
 * every publication so old versions stay auditable while the live schedule is
 * never overwritten. Append-only: the row has no deleted_at column.
 */
class TimetableVersion extends Model
{
    use HasFactory;
    use HasSchoolId;

    protected $table = 'timetable_versions';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'school_id',
        'timetable_id',
        'version',
        'status',
        'snapshot',
        'reason',
        'created_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'snapshot' => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Timetable\TimetableVersionFactory();
    }

    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }
}
