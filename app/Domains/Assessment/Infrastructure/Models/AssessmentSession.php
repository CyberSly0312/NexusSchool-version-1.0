<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Models;

use App\Domains\Assessment\Domain\Enums\AssessmentSessionStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One administration instance of an assessment on a concrete date (SPR-009).
 * Supporting entity: the Assessment aggregate stays the source of truth.
 */
class AssessmentSession extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'assessment_sessions';

    protected $fillable = [
        'uuid',
        'school_id',
        'assessment_id',
        'title',
        'date',
        'starts_at',
        'ends_at',
        'room_id',
        'staff_id',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'status' => AssessmentSessionStatus::class,
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Assessment\AssessmentSessionFactory();
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function isActive(): bool
    {
        return $this->status?->isActive() ?? false;
    }
}
