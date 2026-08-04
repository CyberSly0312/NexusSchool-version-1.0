<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Infrastructure\Models;

use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Enrollment\Domain\Enums\TransferType;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Internal (class/section) or inter-school transfer record. Immutable history.
 */
class EnrollmentTransfer extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'school_id',
        'enrollment_id',
        'student_id',
        'transfer_type',
        'from_class_id',
        'to_class_id',
        'from_section_id',
        'to_section_id',
        'from_school_id',
        'to_school_id',
        'reason',
        'transfer_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'transfer_type' => TransferType::class,
            'transfer_date' => 'date',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Enrollment\EnrollmentTransferFactory();
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function fromClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'from_class_id');
    }

    public function toClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'to_class_id');
    }

    public function fromSection(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class, 'from_section_id');
    }

    public function toSection(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class, 'to_section_id');
    }
}
