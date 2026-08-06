<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Infrastructure\Models;

use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;
use App\Domains\ReportCard\Domain\Enums\ReportCardType;
use App\Domains\ReportCard\Domain\Exceptions\InvalidStatusTransitionException;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aggregate root of the Report Card engine (SPR-011).
 *
 * A report card run covers every active student of one class (optionally one
 * section) for one period. Invariants are enforced by the application actions,
 * never by the model itself: a forward-only lifecycle (Draft → Generated →
 * Published → Archived) with an audited Regenerate that re-composes a
 * generated or published run.
 */
class ReportCard extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'report_cards';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_calendar_year_id',
        'academic_calendar_term_id',
        'school_class_id',
        'class_section_id',
        'report_card_type',
        'status',
        'created_by',
        'generated_at',
        'published_at',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'report_card_type' => ReportCardType::class,
            'status' => ReportCardStatus::class,
            'generated_at' => 'datetime',
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\ReportCard\ReportCardFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function entries(): HasMany
    {
        return $this->hasMany(ReportCardEntry::class, 'report_card_id');
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear::class, 'academic_calendar_year_id');
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm::class, 'academic_calendar_term_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Academic\Infrastructure\Models\SchoolClass::class, 'school_class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Academic\Infrastructure\Models\ClassSection::class, 'class_section_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Lifecycle helpers
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === ReportCardStatus::Draft;
    }

    public function isGenerated(): bool
    {
        return $this->status === ReportCardStatus::Generated;
    }

    public function isPublished(): bool
    {
        return $this->status === ReportCardStatus::Published;
    }

    public function isArchived(): bool
    {
        return $this->status === ReportCardStatus::Archived;
    }

    public function isImmutable(): bool
    {
        return $this->status->isImmutable();
    }

    public function canTransitionTo(ReportCardStatus $to): bool
    {
        return $to->allowsTransitionFrom($this->status);
    }

    /**
     * @throws InvalidStatusTransitionException
     */
    public function assertCanTransitionTo(ReportCardStatus $to): void
    {
        if (! $this->canTransitionTo($to)) {
            throw InvalidStatusTransitionException::from($this->status, $to);
        }
    }

    public function displayName(): string
    {
        $class = $this->schoolClass?->name ?? '#'.$this->school_class_id;

        return sprintf('%s — %s', $class, $this->status->value);
    }
}
