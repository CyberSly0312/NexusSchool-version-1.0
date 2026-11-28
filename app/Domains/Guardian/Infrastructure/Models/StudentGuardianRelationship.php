<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Infrastructure\Models;

use App\Domains\Guardian\Application\Services\GuardianLinkProvenance;
use App\Domains\Guardian\Domain\Enums\RelationshipPortalAccessStatus;
use App\Domains\Guardian\Domain\Enums\RelationshipVerificationStatus;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * First-class link between a Guardian aggregate and a Student aggregate.
 *
 * The relationship carries the business meaning: a configurable relationship
 * type and role flags (primary, emergency contact, authorized pickup,
 * financial responsible, notification recipient, medical contact). Rows are
 * soft-deleted only, so past links survive as history.
 *
 * @property RelationshipVerificationStatus $verification_status
 * @property RelationshipPortalAccessStatus $portal_access_status
 */
class StudentGuardianRelationship extends Model
{
    use HasFactory;
    use HasSchoolId;
    use SoftDeletes;

    protected $table = 'student_guardians';

    protected $fillable = [
        'uuid',
        'school_id',
        'student_id',
        'guardian_id',
        'relationship_type_id',
        'name',
        'phone',
        'email',
        'relationship',
        'is_primary',
        'is_emergency_contact',
        'is_authorized_pickup',
        'is_financial_responsible',
        'is_notification_recipient',
        'is_medical_contact',
        'emergency_contact_priority',
        'pickup_valid_from',
        'pickup_valid_until',
        'relationship_start_date',
        'relationship_end_date',
        'notes',
        'verification_status',
        'portal_access_status',
        'verified_at',
        'portal_authorized_at',
        'portal_revoked_at',
        'verification_evidence_code',
        'authorization_reason_code',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'is_emergency_contact' => 'boolean',
            'is_authorized_pickup' => 'boolean',
            'is_financial_responsible' => 'boolean',
            'is_notification_recipient' => 'boolean',
            'is_medical_contact' => 'boolean',
            'pickup_valid_from' => 'date',
            'pickup_valid_until' => 'date',
            'relationship_start_date' => 'date',
            'relationship_end_date' => 'date',
            'verification_status' => RelationshipVerificationStatus::class,
            'portal_access_status' => RelationshipPortalAccessStatus::class,
            'verified_at' => 'datetime',
            'portal_authorized_at' => 'datetime',
            'portal_revoked_at' => 'datetime',
        ];
    }

    protected static function newFactory(): Factory
    {
        return new \Database\Factories\Guardian\StudentGuardianRelationshipFactory();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    public function relationshipType(): BelongsTo
    {
        return $this->belongsTo(RelationshipType::class, 'relationship_type_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(StudentGuardianRelationshipHistory::class, 'student_guardian_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeEmergencyContacts($query)
    {
        return $query->where('is_emergency_contact', true);
    }

    public function scopeAuthorizedPickups($query)
    {
        return $query->where('is_authorized_pickup', true);
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForGuardian($query, int $guardianId)
    {
        return $query->where('guardian_id', $guardianId);
    }

    /**
     * Only durable, independently verified, explicitly authorized and currently
     * effective relationships can grant Parent portal access.
     */
    public function scopePortalAuthorized($query, ?string $date = null)
    {
        $target = $date !== null
            ? \Illuminate\Support\Carbon::parse($date)->toDateString()
            : \Illuminate\Support\Carbon::today()->toDateString();

        return $query
            ->whereNotNull('guardian_id')
            ->where('verification_status', RelationshipVerificationStatus::Verified->value)
            ->where('portal_access_status', RelationshipPortalAccessStatus::Authorized->value)
            ->whereNotNull('relationship_start_date')
            ->whereDate('relationship_start_date', '<=', $target)
            ->where(function ($query) use ($target): void {
                $query->whereNull('relationship_end_date')
                    ->orWhereDate('relationship_end_date', '>=', $target);
            })
            ->whereExists(function ($query): void {
                $query->select(DB::raw(1))
                    ->from('student_guardian_relationship_history')
                    ->whereColumn('student_guardian_relationship_history.student_guardian_id', 'student_guardians.id')
                    ->whereColumn('student_guardian_relationship_history.school_id', 'student_guardians.school_id')
                    ->whereColumn('student_guardian_relationship_history.student_id', 'student_guardians.student_id')
                    ->whereColumn('student_guardian_relationship_history.guardian_id', 'student_guardians.guardian_id')
                    ->where('student_guardian_relationship_history.event', 'linked')
                    ->whereNotNull('student_guardian_relationship_history.user_id');
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    public function isEmergencyContact(): bool
    {
        return $this->is_emergency_contact;
    }

    public function isAuthorizedPickup(): bool
    {
        return $this->is_authorized_pickup;
    }

    public function isFinancialResponsible(): bool
    {
        return $this->is_financial_responsible;
    }

    public function isNotificationRecipient(): bool
    {
        return $this->is_notification_recipient;
    }

    public function isMedicalContact(): bool
    {
        return $this->is_medical_contact;
    }

    /**
     * A relationship is current while it has no end date or the end date has
     * not been reached yet.
     */
    public function isCurrent(?string $date = null): bool
    {
        if ($this->relationship_end_date === null) {
            return true;
        }

        $target = $date !== null
            ? \Illuminate\Support\Carbon::parse($date)
            : \Illuminate\Support\Carbon::today();

        return $target->lte($this->relationship_end_date);
    }

    /**
     * Whether this relationship may currently authorize Parent child access.
     * This method is intentionally strict and fails closed.
     */
    public function isPortalAuthorized(?string $date = null): bool
    {
        if ($this->trashed() || $this->guardian_id === null) {
            return false;
        }

        if ($this->verification_status !== RelationshipVerificationStatus::Verified) {
            return false;
        }

        if ($this->portal_access_status !== RelationshipPortalAccessStatus::Authorized) {
            return false;
        }

        if (! $this->hasTrustedLinkProvenance()) {
            return false;
        }

        if ($this->relationship_start_date === null) {
            return false;
        }

        $target = $date !== null
            ? \Illuminate\Support\Carbon::parse($date)
            : \Illuminate\Support\Carbon::today();

        return $target->gte($this->relationship_start_date) && $this->isCurrent($date);
    }

    /**
     * A durable row is trusted only when the school-scoped link operation wrote
     * an attributed `linked` history row with matching tenant and target IDs.
     */
    public function hasTrustedLinkProvenance(): bool
    {
        $link = $this->history()
            ->where('event', 'linked')
            ->whereNotNull('user_id')
            ->where('school_id', $this->school_id)
            ->where('student_id', $this->student_id)
            ->where('guardian_id', $this->guardian_id)
            ->first();

        return $link instanceof StudentGuardianRelationshipHistory
            && GuardianLinkProvenance::isTrustedHistory($link);
    }

    /**
     * An authorized-pickup grant is effective today when the pickup window
     * covers the given date (defaults to today).
     */
    public function isPickupValid(?string $date = null): bool
    {
        if (! $this->is_authorized_pickup) {
            return false;
        }

        $window = new \App\Domains\Guardian\Domain\ValueObjects\PickupWindow(
            validFrom: $this->pickup_valid_from?->toDateString(),
            validUntil: $this->pickup_valid_until?->toDateString(),
        );

        return $window->isValidAt($date);
    }

    public function displayGuardian(): string
    {
        return $this->guardian?->displayName() ?? (string) $this->name;
    }
}
