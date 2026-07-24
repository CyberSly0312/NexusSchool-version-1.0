<?php

declare(strict_types=1);

namespace App\Domains\School\Models;

use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Enums\SchoolType;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Database\Factories\SchoolFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant;

class School extends Tenant implements TenantWithDatabase
{
    use HasDatabase;
    use HasDomains;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected static function newFactory(): SchoolFactory
    {
        return SchoolFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (School $school) {
            if (empty($school->uuid)) {
                $school->uuid = \Illuminate\Support\Str::ulid()->toString();
            }
        });
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'uuid',
            'name',
            'slug',
            'database',
            'domain',
            'email',
            'phone',
            'address',
            'city',
            'state',
            'country',
            'postal_code',
            'timezone',
            'currency',
            'locale',
            'logo_url',
            'registration_number',
            'school_type',
            'status',
            'settings',
            'metadata',
        ];
    }

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'database',
        'domain',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'timezone',
        'currency',
        'locale',
        'logo_url',
        'registration_number',
        'school_type',
        'status',
        'settings',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'metadata' => 'array',
            'status' => SchoolStatus::class,
            'school_type' => SchoolType::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? Str::slug($this->name),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function studentEnrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', SchoolStatus::Active);
    }

    public function scopePending($query)
    {
        return $query->where('status', SchoolStatus::Pending);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', SchoolStatus::Suspended);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === SchoolStatus::Active;
    }

    public function isPending(): bool
    {
        return $this->status === SchoolStatus::Pending;
    }

    public function isSuspended(): bool
    {
        return $this->status === SchoolStatus::Suspended;
    }
}
