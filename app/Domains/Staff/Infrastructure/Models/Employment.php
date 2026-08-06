<?php

declare(strict_types=1);

namespace App\Domains\Staff\Infrastructure\Models;

use App\Domains\Staff\Domain\Enums\ContractType;
use App\Domains\Staff\Domain\Enums\EmploymentStatus;
use App\Domains\Staff\Domain\Enums\EmploymentType;
use App\Domains\Staff\Domain\ValueObjects\ContractPeriod;
use App\Models\Department;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Employment contract of a Staff member within the organization. Exactly one
 * employment may be active (non-terminated) per staff member; the contract
 * period is time-aware and the full lifecycle is recorded immutably in
 * EmploymentHistory.
 */
class Employment extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'employments';

    protected $fillable = [
        'uuid',
        'school_id',
        'staff_id',
        'position_id',
        'department_id',
        'employment_type',
        'contract_type',
        'status',
        'salary_scale',
        'salary_amount',
        'contract_start_date',
        'contract_end_date',
        'termination_date',
        'termination_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'contract_type' => ContractType::class,
            'status' => EmploymentStatus::class,
            'salary_amount' => 'decimal:2',
            'contract_start_date' => 'date',
            'contract_end_date' => 'date',
            'termination_date' => 'date',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Staff\EmploymentFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(EmploymentHistory::class, 'employment_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'employment_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->whereNull('termination_date');
    }

    public function scopeTerminated($query)
    {
        return $query->whereNotNull('termination_date');
    }

    public function scopeForStaff($query, int $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function period(): ContractPeriod
    {
        return new ContractPeriod(
            $this->contract_start_date?->format('Y-m-d') ?? '',
            $this->contract_end_date?->format('Y-m-d'),
        );
    }

    public function isActive(): bool
    {
        return $this->status->isCurrent() && $this->termination_date === null;
    }

    public function isTerminated(): bool
    {
        return $this->status->isTerminated() || $this->termination_date !== null;
    }

    public function isCurrentOn(?string $date = null): bool
    {
        if ($this->isTerminated()) {
            return false;
        }

        return $this->period()->isCurrent($date);
    }
}
