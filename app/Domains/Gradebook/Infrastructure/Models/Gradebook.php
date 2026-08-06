<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Infrastructure\Models;

use App\Domains\Gradebook\Domain\Enums\GradebookStatus;
use App\Domains\Gradebook\Domain\Exceptions\InvalidStatusTransitionException;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aggregate root of the Gradebook engine (SPR-010).
 *
 * A gradebook is the term grade of one subject in one class (optionally one
 * section). Invariants are enforced by the application actions, never by the
 * model itself: a forward-only lifecycle (Draft → Calculated → Locked →
 * Published → Archived) with an explicit, audited Unlock that re-opens a
 * locked gradebook for correction.
 */
class Gradebook extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'gradebooks';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_calendar_year_id',
        'academic_calendar_term_id',
        'academic_subject_id',
        'school_class_id',
        'class_section_id',
        'staff_id',
        'coefficient',
        'status',
        'calculated_at',
        'locked_at',
        'published_at',
        'archived_at',
        'created_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => GradebookStatus::class,
            'coefficient' => 'float',
            'calculated_at' => 'datetime',
            'locked_at' => 'datetime',
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Gradebook\GradebookFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function entries(): HasMany
    {
        return $this->hasMany(GradeEntry::class, 'gradebook_id');
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm::class, 'academic_calendar_term_id');
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear::class, 'academic_calendar_year_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Academic\Infrastructure\Models\AcademicSubject::class, 'academic_subject_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Academic\Infrastructure\Models\SchoolClass::class, 'school_class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Academic\Infrastructure\Models\ClassSection::class, 'class_section_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Staff\Infrastructure\Models\Staff::class, 'staff_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Lifecycle helpers
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === GradebookStatus::Draft;
    }

    public function isCalculated(): bool
    {
        return $this->status === GradebookStatus::Calculated;
    }

    public function isLocked(): bool
    {
        return $this->status === GradebookStatus::Locked;
    }

    public function isPublished(): bool
    {
        return $this->status === GradebookStatus::Published;
    }

    public function isArchived(): bool
    {
        return $this->status === GradebookStatus::Archived;
    }

    public function isImmutable(): bool
    {
        return $this->status->isImmutable();
    }

    public function canTransitionTo(GradebookStatus $to): bool
    {
        return $to->allowsTransitionFrom($this->status);
    }

    /**
     * @throws InvalidStatusTransitionException
     */
    public function assertCanTransitionTo(GradebookStatus $to): void
    {
        if (! $this->canTransitionTo($to)) {
            throw InvalidStatusTransitionException::from($this->status, $to);
        }
    }

    public function displayName(): string
    {
        $subject = $this->subject?->name ?? '#'.$this->academic_subject_id;

        return sprintf('%s — %s', $subject, $this->status->value);
    }
}
