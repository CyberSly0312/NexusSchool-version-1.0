<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Infrastructure\Models;

use App\Domains\Student\Infrastructure\Models\Student;
use App\Traits\HasSchoolId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * First-class link between a Guardian aggregate and a Student aggregate.
 *
 * The relationship carries the business meaning: a configurable relationship
 * type and role flags (primary, emergency contact, authorized pickup,
 * financial responsible, notification recipient, medical contact). Rows are
 * soft-deleted only, so past links survive as history.
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
