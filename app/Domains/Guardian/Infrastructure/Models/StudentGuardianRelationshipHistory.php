<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Immutable business timeline of a student–guardian relationship. Rows are
 * append-only: no update or delete path exists in the domain.
 */
class StudentGuardianRelationshipHistory extends Model
{
    use HasFactory;
    use HasSchoolId;

    protected $table = 'student_guardian_relationship_history';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'school_id',
        'student_guardian_id',
        'student_id',
        'guardian_id',
        'event',
        'description',
        'payload',
        'user_id',
        'user_type',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function relationship(): BelongsTo
    {
        return $this->belongsTo(StudentGuardianRelationship::class, 'student_guardian_id');
    }
}
