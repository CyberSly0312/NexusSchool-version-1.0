<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A teacher remark attached to a score (SPR-009). Kept as an append-only
 * supporting entity so feedback never overwrites earlier remarks.
 */
class AssessmentRemark extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'assessment_remarks';

    protected $fillable = [
        'uuid',
        'school_id',
        'assessment_id',
        'assessment_score_id',
        'remark',
        'remark_type',
        'remark_by',
        'remark_at',
    ];

    protected function casts(): array
    {
        return [
            'remark_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Assessment\AssessmentRemarkFactory();
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function score(): BelongsTo
    {
        return $this->belongsTo(AssessmentScore::class, 'assessment_score_id');
    }
}
