<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Infrastructure\Models;

use App\Contracts\SchoolOwned;
use App\Domains\IdentityAndVerification\Domain\Enums\VerificationStatus;
use App\Domains\IdentityAndVerification\Domain\Exceptions\InvalidVerificationTransitionException;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Verification identity of an AcademicDocument (SPR-016, ADR-022 Decision 2/8).
 *
 * One identity per AcademicDocument, permanently bound to the aggregate. Only
 * the SHA-256 digest of the opaque verification token is stored; the
 * plaintext is returned once at issuance and is never persisted or logged. The
 * record carries only the minimum tenant-routing metadata (school_id +
 * academic_document_id): no student data, grades, content or internal
 * identifiers beyond the document key.
 *
 * @property string $uuid
 * @property string $school_id
 * @property int|string $academic_document_id
 * @property string $token_hash
 * @property VerificationStatus $status
 * @property \Illuminate\Support\Carbon|null $issued_at
 * @property string|null $revocation_reason
 * @property \Illuminate\Support\Carbon|null $revoked_at
 * @property \Illuminate\Support\Carbon|null $replaced_at
 * @property string|null $replaced_by_document_number
 */
class VerificationIdentity extends Model implements SchoolOwned
{
    use HasFactory;
    use HasSchoolId;

    protected $table = 'verification_identities';

    protected $fillable = [
        'uuid',
        'school_id',
        'academic_document_id',
        'token_hash',
        'status',
        'issued_at',
        'revocation_reason',
        'revoked_at',
        'replaced_at',
        'replaced_by_document_number',
    ];

    protected function casts(): array
    {
        return [
            'status' => VerificationStatus::class,
            'issued_at' => 'datetime',
            'revoked_at' => 'datetime',
            'replaced_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\IdentityAndVerification\VerificationIdentityFactory();
    }

    public function isActive(): bool
    {
        return $this->status === VerificationStatus::Active;
    }

    public function canTransitionTo(VerificationStatus $to): bool
    {
        return $to->allowsTransitionFrom($this->status);
    }

    public function assertCanTransitionTo(VerificationStatus $to): void
    {
        if (! $this->canTransitionTo($to)) {
            throw InvalidVerificationTransitionException::fromTo($this->status, $to);
        }
    }
}
