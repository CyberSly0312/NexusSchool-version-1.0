<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Infrastructure\Models;

use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionCategory;
use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One decision of a class council session (SPR-013).
 *
 * A decision validates one student's academic result — or the whole class when
 * no student is bound. An override of the automatic decision snapshots the
 * automatic decision being overridden and carries the mandatory justification,
 * approval and audit trail. Published decisions are immutable; only convened
 * sessions accept new decisions.
 */
class CouncilDecision extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'council_decisions';

    protected $fillable = [
        'uuid',
        'school_id',
        'council_session_id',
        'student_id',
        'decision_type',
        'category',
        'code',
        'label',
        'summary',
        'recommendation',
        'decision_date',
        'is_override',
        'automatic_decision_type',
        'automatic_decision_label',
        'justification',
        'approved_by',
        'approved_at',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'decision_type' => CouncilDecisionType::class,
            'category' => CouncilDecisionCategory::class,
            'decision_date' => 'date',
            'is_override' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\ClassCouncil\CouncilDecisionFactory();
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CouncilSession::class, 'council_session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Student\Infrastructure\Models\Student::class, 'student_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }
}
