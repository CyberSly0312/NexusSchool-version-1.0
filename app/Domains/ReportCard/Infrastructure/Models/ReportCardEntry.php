<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Infrastructure\Models;

use App\Domains\ReportCard\Domain\Enums\ReportCardEntryStatus;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One bulletin of a report card run (one student) (SPR-011).
 *
 * The content column holds the immutable snapshot composed by the engine from
 * the validated upstream aggregates (gradebook, standing, attendance, policies,
 * council). The academic_document_id points at the official document owned by
 * the Academic Communication engine.
 */
class ReportCardEntry extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'report_card_entries';

    protected $fillable = [
        'uuid',
        'school_id',
        'report_card_id',
        'enrollment_id',
        'student_id',
        'content',
        'academic_document_id',
        'academic_document_number',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'status' => ReportCardEntryStatus::class,
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\ReportCard\ReportCardEntryFactory();
    }

    public function reportCard(): BelongsTo
    {
        return $this->belongsTo(ReportCard::class, 'report_card_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Enrollment\Infrastructure\Models\Enrollment::class, 'enrollment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Student\Infrastructure\Models\Student::class, 'student_id');
    }

    public function academicDocument(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument::class, 'academic_document_id');
    }
}
