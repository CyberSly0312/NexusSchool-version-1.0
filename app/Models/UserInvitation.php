<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\School\Models\School;
use App\Domains\User\DTOs\UserInvitationIssue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class UserInvitation extends Model
{
    use HasFactory;

    public const PURPOSE_SCHOOL_ADMIN_INITIAL = 'school_admin_initial';
    public const PURPOSE_SCHOOL_ADMIN_RESET = 'school_admin_reset';
    public const PURPOSE_STAFF_INITIAL = 'staff_initial';
    public const PURPOSE_GUARDIAN_INITIAL = 'guardian_initial';
    public const PURPOSE_STUDENT_INITIAL = 'student_initial';

    public const EXPIRATION_HOURS = 48;

    /**
     * Administrative recovery links are deliberately shorter-lived than the
     * initial activation links (24 h instead of 48 h).
     */
    public const RESET_EXPIRATION_HOURS = 24;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'school_id',
        'purpose',
        'token_hash',
        'expires_at',
        'consumed_at',
        'revoked_at',
        'invited_by_user_id',
    ];

    protected $hidden = [
        'token_hash',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    public function isExpired(?Carbon $at = null): bool
    {
        $at ??= now();
        $expiresAt = $this->getAttribute('expires_at');

        if ($expiresAt === null) {
            return false;
        }

        if ($expiresAt instanceof \DateTimeInterface) {
            return Carbon::instance(
                $expiresAt instanceof \DateTimeImmutable
                    ? \DateTime::createFromImmutable($expiresAt)
                    : (new \DateTime())->setTimestamp($expiresAt->getTimestamp())
            )->lte($at);
        }

        return Carbon::parse((string) $expiresAt)->lte($at);
    }

    public function isConsumed(): bool
    {
        return $this->consumed_at !== null;
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isUsable(?Carbon $at = null): bool
    {
        return ! $this->isConsumed() && ! $this->isRevoked() && ! $this->isExpired($at);
    }

    /**
     * Issue a new school-scoped invitation. Any previous outstanding invitation for the same
     * user and purpose is revoked before the replacement is created.
     *
     * The plaintext token is returned only in the DTO and is never persisted.
     */
    public static function issue(
        User $user,
        string $purpose,
        ?User $invitedBy = null,
        ?Carbon $expiresAt = null,
    ): UserInvitationIssue {
        $schoolId = $user->school_id;

        if (! is_string($schoolId) || trim($schoolId) === '') {
            throw new InvalidArgumentException('A school-scoped invitation requires a user with a school.');
        }

        $plainToken = self::generatePlainToken();
        $tokenHash = self::hashToken($plainToken);
        $expiresAt ??= now()->addHours(self::EXPIRATION_HOURS);

        $invitation = DB::transaction(function () use ($user, $schoolId, $purpose, $tokenHash, $expiresAt, $invitedBy): self {
            static::query()
                ->where('user_id', $user->getKey())
                ->where('school_id', $schoolId)
                ->where('purpose', $purpose)
                ->whereNull('consumed_at')
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);

            /** @var self $invitation */
            $invitation = static::query()->create([
                'user_id' => $user->getKey(),
                'school_id' => $schoolId,
                'purpose' => $purpose,
                'token_hash' => $tokenHash,
                'expires_at' => $expiresAt,
                'invited_by_user_id' => $invitedBy?->getKey(),
            ]);

            return $invitation;
        });

        return new UserInvitationIssue($invitation, $plainToken);
    }

    /**
     * Atomically consume a usable invitation. Returns true when consumed by this call.
     *
     * Full activation workflow (password setup, status transition, session handling) is
     * intentionally deferred to a future application service.
     */
    public function consume(?Carbon $at = null): bool
    {
        $at ??= now();

        return DB::transaction(function () use ($at): bool {
            /** @var self|null $fresh */
            $fresh = static::query()
                ->whereKey($this->getKey())
                ->lockForUpdate()
                ->first();

            if (! $fresh instanceof self || ! $fresh->isUsable($at)) {
                return false;
            }

            $fresh->forceFill(['consumed_at' => $at])->save();
            $this->consumed_at = $fresh->consumed_at;

            return true;
        });
    }

    public function revoke(?Carbon $at = null): bool
    {
        $at ??= now();

        if ($this->isConsumed() || $this->isRevoked()) {
            return false;
        }

        $this->forceFill(['revoked_at' => $at])->save();

        return true;
    }

    public static function generatePlainToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public static function hashToken(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }

    public static function findByPlainToken(string $plainToken): ?self
    {
        /** @var self|null $invitation */
        $invitation = static::query()
            ->where('token_hash', self::hashToken($plainToken))
            ->first();

        return $invitation;
    }

    public static function findUsableByPlainToken(string $plainToken, ?Carbon $at = null): ?self
    {
        $invitation = static::findByPlainToken($plainToken);

        if (! $invitation instanceof self || ! $invitation->isUsable($at)) {
            return null;
        }

        return $invitation;
    }

    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            $schoolId = (string) $model->school_id;
            if (trim($schoolId) === '') {
                throw new InvalidArgumentException('School-scoped invitations require a school_id.');
            }

            $user = $model->relationLoaded('user')
                ? $model->user
                : User::query()->find($model->user_id);

            if ($user instanceof User) {
                self::assertUserSchoolMatch($user, $schoolId);
            }
        });
    }

    private static function assertUserSchoolMatch(User $user, string $schoolId): void
    {
        $userSchoolId = $user->school_id;

        if (! is_string($userSchoolId) || trim($userSchoolId) === '') {
            throw new InvalidArgumentException('Invited user must belong to a school.');
        }

        if ((string) $userSchoolId !== (string) $schoolId) {
            throw new InvalidArgumentException('Invitation school must match the invited user school.');
        }
    }
}
