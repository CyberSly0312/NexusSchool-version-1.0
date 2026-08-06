<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Confidential deliberation record of a class council session (SPR-013).
 *
 * Deliberation records are the private record of the council: they are never
 * exposed through a contract and can never reach a rendered document. Only
 * published decisions leave the council domain.
 */
class CouncilDeliberation extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'council_deliberations';

    protected $fillable = [
        'uuid',
        'school_id',
        'council_session_id',
        'student_id',
        'council_decision_id',
        'member',
        'topic',
        'summary',
        'noted_at',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'noted_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\ClassCouncil\CouncilDeliberationFactory();
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CouncilSession::class, 'council_session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Student\Infrastructure\Models\Student::class, 'student_id');
    }

    public function decision(): BelongsTo
    {
        return $this->belongsTo(CouncilDecision::class, 'council_decision_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }
}
