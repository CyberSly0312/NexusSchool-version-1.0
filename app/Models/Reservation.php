<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Library\Enums\BorrowerType;
use App\Domains\Library\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'book_id',
        'borrower_id',
        'borrower_type',
        'reservation_date',
        'expiry_date',
        'fulfilled_date',
        'status',
        'notes',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Reservation $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'expiry_date' => 'date',
            'fulfilled_date' => 'date',
            'borrower_type' => BorrowerType::class,
            'status' => ReservationStatus::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsExpiredAttribute(): bool
    {
        return $this->status === ReservationStatus::Pending && $this->expiry_date->isPast();
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

    public function borrower()
    {
        if (! $this->borrower_type) {
            return $this->belongsTo(User::class, 'borrower_id');
        }

        $modelClass = $this->borrower_type->modelClass();

        return $this->belongsTo($modelClass, 'borrower_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', ReservationStatus::Pending);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', ReservationStatus::Pending)->where('expiry_date', '<', now());
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
        return $this->status === ReservationStatus::Pending;
    }

    public function isFulfilled(): bool
    {
        return $this->status === ReservationStatus::Fulfilled;
    }

    public function isExpired(): bool
    {
        return $this->status === ReservationStatus::Pending && $this->expiry_date->isPast();
    }
}
