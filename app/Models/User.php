<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\School\Models\School;
use App\Domains\User\Enums\UserStatus;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property \App\Domains\User\Enums\UserStatus $status
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 */
class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasRoles;
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use Notifiable;

    protected $fillable = [
        'school_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'avatar',
        'password',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->uuid)) {
                $user->uuid = Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFilamentName(): string
    {
        return $this->full_name;
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

    public function guardian(): HasOne
    {
        return $this->hasOne(Guardian::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function isInactive(): bool
    {
        return $this->status === UserStatus::Inactive;
    }

    public function isPending(): bool
    {
        return $this->status === UserStatus::Pending;
    }

    public function isSuspended(): bool
    {
        return $this->status === UserStatus::Suspended;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        // The platform administrator is a central user: it may only use the
        // central panel and must never enter a school (tenant) panel.
        if ($this->hasRole('super_admin')) {
            return $panel->getId() === 'super-admin';
        }

        // The central panel is reserved for platform administrators.
        if ($panel->getId() === 'super-admin') {
            return false;
        }

        $panelRoles = match ($panel->getId()) {
            'admin', 'school-admin' => ['school_admin'],
            'director' => ['director'],
            'teacher' => ['teacher'],
            'student' => ['student'],
            'parent' => ['parent'],
            'accountant' => ['accountant'],
            'secretary' => ['secretary'],
            default => [],
        };

        if (empty($panelRoles) || ! $this->hasAnyRole($panelRoles)) {
            return false;
        }

        // School panels require a valid, active school relationship. A missing,
        // deleted or non-active school must never grant panel access.
        return $this->hasActiveSchool();
    }

    public function hasActiveSchool(): bool
    {
        $school = $this->school;

        if (! $school instanceof School) {
            return false;
        }

        return $school->isActive();
    }
}
