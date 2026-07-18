<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Library\Enums\BorrowerType;
use App\Domains\Library\Enums\FineStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Fine extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'book_loan_id',
        'borrower_id',
        'borrower_type',
        'reason',
        'amount',
        'paid_amount',
        'waived_amount',
        'fine_date',
        'paid_date',
        'waived_date',
        'payment_method',
        'reference_number',
        'status',
        'processed_by',
        'notes',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Fine $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'waived_amount' => 'decimal:2',
            'fine_date' => 'date',
            'paid_date' => 'date',
            'waived_date' => 'date',
            'borrower_type' => BorrowerType::class,
            'status' => FineStatus::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getBalanceAttribute(): float
    {
        return (float) $this->amount - (float) $this->paid_amount - (float) $this->waived_amount;
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->balance <= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function bookLoan(): BelongsTo
    {
        return $this->belongsTo(BookLoan::class);
    }

    public function borrower()
    {
        if (! $this->borrower_type) {
            return $this->belongsTo(User::class, 'borrower_id');
        }

        $modelClass = $this->borrower_type->modelClass();

        return $this->belongsTo($modelClass, 'borrower_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', FineStatus::Pending);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', [FineStatus::Pending, FineStatus::PartiallyPaid]);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeForBorrower($query, int $borrowerId, BorrowerType $type)
    {
        return $query->where('borrower_id', $borrowerId)->where('borrower_type', $type);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === FineStatus::Pending;
    }

    public function isPaid(): bool
    {
        return $this->status === FineStatus::Paid;
    }

    public function isWaived(): bool
    {
        return $this->status === FineStatus::Waived;
    }
}
