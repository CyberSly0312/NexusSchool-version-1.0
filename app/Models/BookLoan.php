<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Library\Enums\BookLoanStatus;
use App\Domains\Library\Enums\BorrowerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BookLoan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'book_copy_id',
        'borrower_id',
        'borrower_type',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'renewal_count',
        'issued_by',
        'returned_to',
        'notes',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (BookLoan $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'loan_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
            'borrower_type' => BorrowerType::class,
            'status' => BookLoanStatus::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === BookLoanStatus::Active && $this->due_date->isPast();
    }

    public function getDaysOverdueAttribute(): int
    {
        if (! $this->is_overdue) {
            return 0;
        }

        return (int) $this->due_date->diffInDays(now());
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_copy_id', 'id');
    }

    public function borrower()
    {
        if (! $this->borrower_type) {
            return $this->belongsTo(User::class, 'borrower_id');
        }

        $modelClass = $this->borrower_type->modelClass();

        return $this->belongsTo($modelClass, 'borrower_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function returner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', BookLoanStatus::Active);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', BookLoanStatus::Active)->where('due_date', '<', now());
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

    public function isActive(): bool
    {
        return $this->status === BookLoanStatus::Active;
    }

    public function isReturned(): bool
    {
        return $this->status === BookLoanStatus::Returned;
    }

    public function isOverdue(): bool
    {
        return $this->status === BookLoanStatus::Active && $this->due_date->isPast();
    }

    public function isLost(): bool
    {
        return $this->status === BookLoanStatus::Lost;
    }
}
