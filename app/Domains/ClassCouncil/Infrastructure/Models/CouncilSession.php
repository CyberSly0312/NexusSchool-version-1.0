<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Infrastructure\Models;

use App\Contracts\SchoolOwned;
use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\ClassCouncil\Domain\Exceptions\InvalidStatusTransitionException;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aggregate root of the Class Council engine (SPR-013).
 *
 * A council session convenes a class (optionally one section) for one period
 * and validates the published academic results of its students. Invariants are
 * enforced by the application actions, never by the model itself: a forward-only
 * lifecycle (Draft → Convened → Published → Archived) in which only convened
 * sessions accept decisions and published sessions are immutable.
 */
class CouncilSession extends Model implements SchoolOwned
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'council_sessions';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_calendar_year_id',
        'academic_calendar_term_id',
        'school_class_id',
        'class_section_id',
        'reference',
        'status',
        'held_at',
        'published_at',
        'archived_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => CouncilSessionStatus::class,
            'held_at' => 'datetime',
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\ClassCouncil\CouncilSessionFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function decisions(): HasMany
    {
        return $this->hasMany(CouncilDecision::class, 'council_session_id');
    }

    public function deliberations(): HasMany
    {
        return $this->hasMany(CouncilDeliberation::class, 'council_session_id');
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
        return $this->status === CouncilSessionStatus::Draft;
    }

    public function isConvened(): bool
    {
        return $this->status === CouncilSessionStatus::Convened;
    }

    public function isPublished(): bool
    {
        return $this->status === CouncilSessionStatus::Published;
    }

    public function isArchived(): bool
    {
        return $this->status === CouncilSessionStatus::Archived;
    }

    public function isImmutable(): bool
    {
        return $this->status->isImmutable();
    }

    public function canTransitionTo(CouncilSessionStatus $to): bool
    {
        return $to->allowsTransitionFrom($this->status);
    }

    /**
     * @throws InvalidStatusTransitionException
     */
    public function assertCanTransitionTo(CouncilSessionStatus $to): void
    {
        if (! $this->canTransitionTo($to)) {
            throw InvalidStatusTransitionException::from($this->status, $to);
        }
    }

    public function displayName(): string
    {
        $class = $this->schoolClass?->name ?? '#'.$this->school_class_id;

        return sprintf('%s — %s', $this->reference, $class);
    }
}
