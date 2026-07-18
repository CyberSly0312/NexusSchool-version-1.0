<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Library\Enums\BookCopyCondition;
use App\Domains\Library\Enums\BookCopyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BookCopy extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'book_id',
        'barcode',
        'inventory_number',
        'acquisition_date',
        'purchase_cost',
        'condition',
        'is_available',
        'status',
        'notes',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (BookCopy $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'acquisition_date' => 'date',
            'purchase_cost' => 'decimal:2',
            'is_available' => 'boolean',
            'condition' => BookCopyCondition::class,
            'status' => BookCopyStatus::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(BookLoan::class);
    }

    public function currentLoan(): HasOne
    {
        return $this->hasOne(BookLoan::class)->latestOfMany();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('status', BookCopyStatus::Available);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeForBook($query, int $bookId)
    {
        return $query->where('book_id', $bookId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isAvailable(): bool
    {
        return $this->is_available && $this->status === BookCopyStatus::Available;
    }

    public function isBorrowed(): bool
    {
        return $this->status === BookCopyStatus::Borrowed;
    }

    public function isReserved(): bool
    {
        return $this->status === BookCopyStatus::Reserved;
    }

    public function isArchived(): bool
    {
        return $this->status === BookCopyStatus::Archived;
    }

    public function canTransitionTo(BookCopyStatus $target): bool
    {
        return $this->status->canTransitionTo($target);
    }
}
