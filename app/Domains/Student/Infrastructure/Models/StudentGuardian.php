<?php

declare(strict_types=1);

namespace App\Domains\Student\Infrastructure\Models;

use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Pivot between a student and an embedded guardian reference. Kept future-ready:
 * a `guardian_id` may later link to a first-class Guardian entity.
 */
class StudentGuardian extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'school_id',
        'student_id',
        'guardian_id',
        'name',
        'phone',
        'email',
        'relationship',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
