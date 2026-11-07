<?php

declare(strict_types=1);

namespace App\Domains\School\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A one-time, hashed setup token for a school administrator.
 *
 * Only the SHA-256 hash of the token is persisted. The plaintext token exists
 * exclusively in the one-time setup URL returned to the authorized Super Admin
 * who triggered provisioning/recovery. Tokens expire and are single-use.
 *
 * @property string $school_id
 * @property int $user_id
 * @property string $token_hash
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property int|null $created_by
 */
class SchoolAdminSetupToken extends Model
{
    use HasUlids;

    protected $fillable = [
        'school_id',
        'user_id',
        'token_hash',
        'expires_at',
        'used_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return ! $this->isUsed() && ! $this->isExpired();
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query
            ->whereNull('used_at')
            ->where('expires_at', '>', now());
    }
}
