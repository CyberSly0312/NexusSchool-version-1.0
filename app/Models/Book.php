<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Library\Enums\BookStatus;
use App\Domains\School\Models\School;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'isbn',
        'title',
        'subtitle',
        'description',
        'language',
        'edition',
        'publication_year',
        'publisher_id',
        'book_category_id',
        'shelf_location',
        'cover_image',
        'status',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Book $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'publication_year' => 'integer',
            'status' => BookStatus::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'book_author', 'book_id', 'author_id');
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }

    public function loans(): HasMany
    {
        return $this->hasManyThrough(BookLoan::class, BookCopy::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAvailableCopiesCountAttribute(): int
    {
        return $this->copies()->where('is_available', true)->count();
    }

    public function getTotalCopiesCountAttribute(): int
    {
        return $this->copies()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', BookStatus::Active);
    }

    public function scopeForSchool($query, string $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === BookStatus::Active;
    }

    public function hasAvailableCopies(): bool
    {
        return $this->copies()->where('is_available', true)->exists();
    }
}
