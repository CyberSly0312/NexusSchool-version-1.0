<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Infrastructure\Models;

use App\Contracts\SchoolOwned;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyScope;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;
use App\Domains\AcademicPolicy\Domain\ValueObjects\PolicyValue;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One school-owned academic rule (SPR-009.5).
 *
 * The engine is the single origin of academic rules (ARCHITECTURE_DECISIONS
 * §6). Each policy is bound to exactly one school; the value is stored as JSON
 * and normalized onto the declared value type when read. Soft deletes keep
 * archived policies visible to the audit trail.
 *
 * @property string|null $uuid
 * @property string $key
 * @property string $label
 * @property string|null $description
 * @property AcademicPolicyValueType $value_type
 * @property mixed $value
 * @property AcademicPolicyScope $scope
 * @property int|null $academic_year_id
 * @property int|null $academic_subject_id
 * @property bool $is_active
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class AcademicPolicy extends Model implements SchoolOwned
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'academic_policies';

    protected $fillable = [
        'uuid',
        'school_id',
        'key',
        'label',
        'description',
        'value_type',
        'value',
        'scope',
        'academic_year_id',
        'academic_subject_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'value_type' => AcademicPolicyValueType::class,
            'scope' => AcademicPolicyScope::class,
            'is_active' => 'boolean',
        ];
    }

    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $stored): mixed => PolicyValue::fromStored(
                $this->value_type,
                json_decode((string) $stored, true),
            )->asRaw(),
            set: fn (mixed $value): string => json_encode($value, JSON_THROW_ON_ERROR),
        );
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\AcademicPolicy\AcademicPolicyFactory();
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear::class, 'academic_year_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Academic\Infrastructure\Models\AcademicSubject::class, 'academic_subject_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
