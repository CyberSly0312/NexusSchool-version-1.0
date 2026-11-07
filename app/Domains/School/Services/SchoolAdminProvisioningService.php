<?php

declare(strict_types=1);

namespace App\Domains\School\Services;

use App\Domains\School\DTOs\SchoolAdminProvisioningResult;
use App\Domains\School\Models\School;
use App\Domains\School\Models\SchoolAdminSetupToken;
use App\Domains\System\Enums\AuditAction;
use App\Domains\System\Services\AuditService;
use App\Domains\User\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Provisioning and recovery for the initial School Administrator.
 *
 * Security invariants:
 *  - no password is ever generated, stored or displayed in plaintext;
 *  - the account is created with an unusable random password and a one-time,
 *    hashed setup token so the administrator chooses their own password;
 *  - provisioning is idempotent: an existing School Admin is never duplicated;
 *  - recovery never touches unrelated users and never resets a password until
 *    the administrator redeems a freshly issued, single-use link;
 *  - every provisioning/recovery/password event is audited.
 */
class SchoolAdminProvisioningService
{
    public const ROLE = 'school_admin';

    /**
     * Setup links are short-lived. Twenty-four hours leaves enough time for the
     * link to be delivered securely without becoming a long-term credential.
     */
    public const TOKEN_TTL_MINUTES = 60 * 24;

    /**
     * Platform-only capabilities that must never be granted to a school
     * administrator. School Admins receive every other declared permission so
     * they can operate their own school without reaching platform operations.
     *
     * @var list<string>
     */
    private const EXCLUDED_PERMISSIONS = [
        'school.create',
        'school.delete',
        'permission.create',
        'permission.update',
        'permission.delete',
        'audit_log.create',
        'audit_log.update',
        'audit_log.delete',
    ];

    /**
     * @var list<string>
     */
    private const EXCLUDED_PERMISSION_PREFIXES = [
        'api.',
        'backup.',
        'maintenance_window.',
    ];

    /**
     * Create the initial School Admin for a school.
     *
     * When an administrator already exists for the school this method returns
     * that account unchanged (`created = false`) and does not issue a new link;
     * recovery links are issued explicitly through {@see issueSetupLink()}.
     */
    public function provision(
        School $school,
        string $name,
        string $email,
        ?User $actor = null,
    ): SchoolAdminProvisioningResult {
        $name = trim($name);
        $email = Str::lower(trim($email));

        if ($name === '') {
            throw ValidationException::withMessages([
                'admin_name' => 'The administrator name is required.',
            ]);
        }

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw ValidationException::withMessages([
                'admin_email' => 'A valid administrator email address is required.',
            ]);
        }

        return DB::transaction(function () use ($school, $name, $email, $actor) {
            $existing = $this->findSchoolAdmin($school);

            if ($existing instanceof User) {
                return new SchoolAdminProvisioningResult($existing, false);
            }

            // Never take over an unrelated account. The email must be free;
            // reusing an existing user could grant School Admin privileges to
            // someone who was not intended to receive them.
            $conflict = User::withTrashed()->where('email', $email)->first();

            if ($conflict instanceof User) {
                throw ValidationException::withMessages([
                    'admin_email' => 'This email address is already associated with another account.',
                ]);
            }

            [$firstName, $lastName] = $this->splitName($name);

            $user = User::query()->create([
                'school_id' => $school->getKey(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                // Unusable random password. The account cannot authenticate
                // until the administrator redeems a one-time setup link.
                'password' => Str::random(64),
                'status' => UserStatus::Pending,
            ]);

            $role = Role::findOrCreate(self::ROLE, 'web');
            $user->assignRole($role);
            $this->grantSchoolAdminPermissions($role);

            [$plainToken, $expiresAt] = $this->createSetupToken($school, $user, $actor);

            $this->audit(
                action: AuditAction::Provisioned,
                event: 'school_admin.provisioned',
                description: 'Initial School Admin account provisioned with a one-time setup link.',
                school: $school,
                user: $user,
                actor: $actor,
                newValues: [
                    'email' => $user->email,
                    'role' => self::ROLE,
                    'status' => $user->status->value,
                ],
            );

            return new SchoolAdminProvisioningResult(
                user: $user->refresh(),
                created: true,
                setupUrl: $this->buildSetupUrl($school, $plainToken),
                expiresAt: $expiresAt,
            );
        });
    }

    /**
     * Issue a fresh one-time setup link for an existing School Admin.
     *
     * The existing password is never read, displayed or changed here. Any
     * previous unused link for the same administrator is revoked so only the
     * newest link can be redeemed.
     */
    public function issueSetupLink(School $school, ?User $actor = null): SchoolAdminProvisioningResult
    {
        return DB::transaction(function () use ($school, $actor) {
            $user = $this->findRecoverableSchoolAdmin($school);

            if (! $user instanceof User) {
                throw ValidationException::withMessages([
                    'admin' => 'No recoverable School Admin exists for this school. Provision one first.',
                ]);
            }

            [$plainToken, $expiresAt] = $this->createSetupToken($school, $user, $actor);

            $this->audit(
                action: AuditAction::SetupLinkIssued,
                event: 'school_admin.setup_link_issued',
                description: 'A one-time School Admin setup link was issued. No password was changed.',
                school: $school,
                user: $user,
                actor: $actor,
            );

            return new SchoolAdminProvisioningResult(
                user: $user,
                created: false,
                setupUrl: $this->buildSetupUrl($school, $plainToken),
                expiresAt: $expiresAt,
            );
        });
    }

    /**
     * Resolve a plaintext setup token for the current tenant.
     */
    public function findValidByToken(string $plainToken): ?SchoolAdminSetupToken
    {
        if ($plainToken === '') {
            return null;
        }

        $token = SchoolAdminSetupToken::query()
            ->with(['user', 'school'])
            ->where('token_hash', hash('sha256', $plainToken))
            ->valid()
            ->first();

        if (! $token instanceof SchoolAdminSetupToken) {
            return null;
        }

        if (! $token->user instanceof User || ! $token->school instanceof School) {
            return null;
        }

        if ((string) $token->user->school_id !== (string) $token->school_id) {
            return null;
        }

        if (! $token->user->hasRole(self::ROLE)) {
            return null;
        }

        return $token;
    }

    /**
     * Redeem a one-time setup link: set the administrator's own password,
     * activate the pending account and invalidate every other unused link.
     */
    public function redeem(string $plainToken, string $password): User
    {
        return DB::transaction(function () use ($plainToken, $password) {
            $token = SchoolAdminSetupToken::query()
                ->where('token_hash', hash('sha256', $plainToken))
                ->lockForUpdate()
                ->first();

            if (! $token instanceof SchoolAdminSetupToken || ! $token->isValid()) {
                throw ValidationException::withMessages([
                    'token' => 'This setup link is invalid or has expired.',
                ]);
            }

            $user = User::withTrashed()->find($token->user_id);
            $school = School::query()->find($token->school_id);

            if (! $user instanceof User
                || $user->trashed()
                || ! $school instanceof School
                || (string) $user->school_id !== (string) $token->school_id
                || ! $user->hasRole(self::ROLE)) {
                throw ValidationException::withMessages([
                    'token' => 'This setup link is no longer valid.',
                ]);
            }

            if ($user->status === UserStatus::Suspended) {
                throw ValidationException::withMessages([
                    'token' => 'This account is suspended. Contact the platform administrator.',
                ]);
            }

            $wasPending = $user->status === UserStatus::Pending;

            $user->forceFill([
                'password' => $password,
                'status' => $wasPending ? UserStatus::Active : $user->status,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            $token->forceFill(['used_at' => now()])->save();

            // A setup link is single-use: revoke any sibling unused links.
            SchoolAdminSetupToken::query()
                ->where('user_id', $user->getKey())
                ->whereKeyNot($token->getKey())
                ->whereNull('used_at')
                ->delete();

            $this->audit(
                action: AuditAction::PasswordSet,
                event: 'school_admin.password_set',
                description: 'School Admin completed one-time setup and chose a new password.',
                school: $school,
                user: $user,
                actor: $user,
                newValues: ['status' => $user->status->value],
            );

            return $user->refresh();
        });
    }

    /**
     * The first School Admin of a school, including suspended accounts, so
     * provisioning never creates a duplicate.
     */
    public function findSchoolAdmin(School $school): ?User
    {
        return User::query()
            ->where('school_id', $school->getKey())
            ->whereHas('roles', fn ($query) => $query
                ->where('name', self::ROLE)
                ->where('guard_name', 'web'))
            ->orderBy('id')
            ->first();
    }

    /**
     * The first School Admin who may receive a recovery link.
     */
    public function findRecoverableSchoolAdmin(School $school): ?User
    {
        return User::query()
            ->where('school_id', $school->getKey())
            ->where('status', '!=', UserStatus::Suspended)
            ->whereHas('roles', fn ($query) => $query
                ->where('name', self::ROLE)
                ->where('guard_name', 'web'))
            ->orderBy('id')
            ->first();
    }

    /**
     * Grant the intended school-level permissions to the role additively.
     *
     * `givePermissionTo` is used instead of `syncPermissions` so an operator's
     * deliberate extra grants are preserved.
     */
    private function grantSchoolAdminPermissions(RoleContract $role): void
    {
        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->get()
            ->reject(fn (Permission $permission): bool => $this->isPlatformOnlyPermission($permission->name))
            ->pluck('name')
            ->all();

        if ($permissions !== []) {
            $role->givePermissionTo($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function isPlatformOnlyPermission(string $name): bool
    {
        if (in_array($name, self::EXCLUDED_PERMISSIONS, true)) {
            return true;
        }

        foreach (self::EXCLUDED_PERMISSION_PREFIXES as $prefix) {
            if (str_starts_with($name, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{0: string, 1: \Illuminate\Support\Carbon}
     */
    private function createSetupToken(School $school, User $user, ?User $actor): array
    {
        // Only the newest link may be redeemed.
        SchoolAdminSetupToken::query()
            ->where('user_id', $user->getKey())
            ->whereNull('used_at')
            ->delete();

        $plainToken = bin2hex(random_bytes(32));
        $expiresAt = now()->addMinutes(self::TOKEN_TTL_MINUTES);

        SchoolAdminSetupToken::query()->create([
            'school_id' => $school->getKey(),
            'user_id' => $user->getKey(),
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => $expiresAt,
            'created_by' => $actor?->getKey(),
        ]);

        return [$plainToken, $expiresAt];
    }

    private function buildSetupUrl(School $school, string $plainToken): string
    {
        $host = (string) ($school->domains()->value('domain') ?? $school->domain);

        if ($host === '') {
            throw new \LogicException('The school has no registered domain to build a setup URL.');
        }

        $scheme = request()->getScheme() ?: 'https';

        return sprintf(
            '%s://%s/school-admin/setup/%s',
            $scheme,
            $host,
            rawurlencode($plainToken),
        );
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', $name) ?: [];
        $firstName = (string) array_shift($parts);
        $lastName = $parts === [] ? $firstName : implode(' ', $parts);

        return [$firstName, $lastName];
    }

    private function audit(
        AuditAction $action,
        string $event,
        string $description,
        School $school,
        User $user,
        ?User $actor,
        array $newValues = [],
    ): void {
        app(AuditService::class)->record([
            'school_id' => $school->getKey(),
            'auditable_type' => User::class,
            'auditable_id' => (string) $user->getKey(),
            'action' => $action->value,
            'event' => $event,
            'description' => $description,
            'new_values' => $newValues === [] ? null : $newValues,
            'user_id' => $actor !== null ? (string) $actor->getKey() : null,
            'user_type' => $actor !== null ? User::class : null,
        ]);
    }
}
