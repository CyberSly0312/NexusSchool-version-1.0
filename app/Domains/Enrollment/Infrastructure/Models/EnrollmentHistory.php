<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Infrastructure\Models;

use App\Domains\Student\Infrastructure\Models\Student;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Immutable business timeline of an enrollment. Rows are append-only: no
 * update or delete path exists in the domain (only GDPR soft-delete).
 */
class EnrollmentHistory extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'enrollment_history';

    protected $fillable = [
        'uuid',
        'school_id',
        'enrollment_id',
        'student_id',
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

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
