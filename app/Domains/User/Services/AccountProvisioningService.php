<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use App\Domains\Role\Policies\SchoolAdminRolePolicy;
use App\Domains\School\Models\School;
use App\Domains\User\DTOs\AccountProvisioningCommand;
use App\Domains\User\DTOs\AccountProvisioningContext;
use App\Domains\User\DTOs\AccountProvisioningResult;
use App\Domains\User\Enums\UserStatus;
use App\Domains\User\Exceptions\AccountProvisioningException;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AccountProvisioningService
{
    /**
     * School-admin provisioning is intentionally conservative in Slice 2.
     * Roles outside this allowlist are rejected because the V1 matrix is ambiguous
     * for director/vice_director/librarian/registrar/school_admin.
     *
     * @var list<string>
     */
    public const SCHOOL_ADMIN_ALLOWED_ROLES = SchoolAdminRolePolicy::ALLOWED_ROLES;

    /**
     * Platform provisioning in Slice 2 is limited to the explicitly specified case.
     *
     * @var list<string>
     */
    private const PLATFORM_ALLOWED_ROLES = [
        'school_admin',
    ];

    public function provision(AccountProvisioningCommand $command, AccountProvisioningContext $context): AccountProvisioningResult
    {
        // Normalization belongs to the domain service so every presentation
        // surface (and every future caller) shares the same identity rule.
        $email = Str::lower(trim($command->email));

        $this->assertActorIsActive($context->actor);

        $school = $this->resolveAndValidateSchool($command, $context);
        $this->assertEmailAvailable($email);
        $role = $this->resolveAndValidateRole($command->roleName, $context);

        return DB::transaction(function () use ($command, $context, $school, $role, $email): AccountProvisioningResult {
            $user = User::query()->create([
                'school_id' => $school->getKey(),
                'first_name' => $command->firstName,
                'last_name' => $command->lastName,
                'email' => $email,
                'phone' => $command->phone,
                'password' => Hash::make(Str::random(64)),
                'status' => UserStatus::Pending,
            ]);

            $user->assignRole($role);

            $invitationIssue = UserInvitation::issue(
                user: $user,
                purpose: $this->purposeForRole($role->name),
                invitedBy: $context->actor,
            );

            return new AccountProvisioningResult($user->refresh(), $invitationIssue);
        });
    }

    private function assertActorIsActive(User $actor): void
    {
        if ($actor->trashed() || ! $actor->isActive()) {
            throw new AccountProvisioningException(
                'Provisioning actor must be active.',
                AccountProvisioningException::REASON_ACTOR,
            );
        }
    }

    private function resolveAndValidateSchool(AccountProvisioningCommand $command, AccountProvisioningContext $context): School
    {
        $schoolId = trim($command->schoolId);
        if ($schoolId === '') {
            throw new AccountProvisioningException(
                'Target school is required.',
                AccountProvisioningException::REASON_SCHOOL,
            );
        }

        if ($context->isSchoolAdminProvisioning()) {
            $actorSchoolId = (string) $context->actor->school_id;
            if ($actorSchoolId === '' || $actorSchoolId !== $schoolId) {
                throw new AccountProvisioningException(
                    'School administrators may only provision accounts within their own school.',
                    AccountProvisioningException::REASON_SCHOOL,
                );
            }
        }

        $school = School::query()->find($schoolId);
        if (! $school instanceof School) {
            throw new AccountProvisioningException(
                'Target school does not exist.',
                AccountProvisioningException::REASON_SCHOOL,
            );
        }

        if (! $school->isActive()) {
            throw new AccountProvisioningException(
                'Target school must be active.',
                AccountProvisioningException::REASON_SCHOOL,
            );
        }

        if ($context->isSchoolAdminProvisioning()) {
            $actorSchoolId = (string) $context->actor->school_id;
            if ((string) $school->getKey() !== $actorSchoolId) {
                throw new AccountProvisioningException(
                    'School administrators may only provision accounts within their own school.',
                    AccountProvisioningException::REASON_SCHOOL,
                );
            }
        }

        return $school;
    }

    private function assertEmailAvailable(string $email): void
    {
        $normalized = Str::lower(trim($email));
        if ($normalized === '') {
            throw new AccountProvisioningException(
                'Email is required.',
                AccountProvisioningException::REASON_EMAIL,
            );
        }

        $exists = User::withTrashed()->where('email', $normalized)->exists();
        if ($exists) {
            throw new AccountProvisioningException(
                'A user with this email already exists.',
                AccountProvisioningException::REASON_EMAIL,
            );
        }
    }

    private function resolveAndValidateRole(string $roleName, AccountProvisioningContext $context): Role
    {
        $normalized = Str::lower(trim($roleName));
        if ($normalized === '') {
            throw new AccountProvisioningException(
                'Role is required.',
                AccountProvisioningException::REASON_ROLE,
            );
        }

        $role = Role::query()->where('name', $normalized)->where('guard_name', 'web')->first();
        if (! $role instanceof Role) {
            throw new AccountProvisioningException(
                "Role [{$normalized}] does not exist.",
                AccountProvisioningException::REASON_ROLE,
            );
        }

        if ($context->isPlatformProvisioning()) {
            if ($normalized === 'super_admin') {
                throw new AccountProvisioningException(
                    'The super_admin role cannot be assigned through account provisioning.',
                    AccountProvisioningException::REASON_ROLE,
                );
            }

            if (! in_array($normalized, self::PLATFORM_ALLOWED_ROLES, true)) {
                throw new AccountProvisioningException(
                    'This role is not allowed for platform provisioning in the current Slice 2 scope.',
                    AccountProvisioningException::REASON_ROLE,
                );
            }

            return $role;
        }

        if ($normalized === 'super_admin') {
            throw new AccountProvisioningException(
                'The super_admin role cannot be assigned through school-level provisioning.',
                AccountProvisioningException::REASON_ROLE,
            );
        }

        if ($normalized === 'school_admin') {
            throw new AccountProvisioningException(
                'School administrators cannot provision school_admin accounts.',
                AccountProvisioningException::REASON_ROLE,
            );
        }

        if (! in_array($normalized, self::SCHOOL_ADMIN_ALLOWED_ROLES, true)) {
            throw new AccountProvisioningException(
                'This role is not allowed for school-admin provisioning in the current Slice 2 scope.',
                AccountProvisioningException::REASON_ROLE,
            );
        }

        return $role;
    }

    private function purposeForRole(string $roleName): string
    {
        return match ($roleName) {
            'school_admin' => UserInvitation::PURPOSE_SCHOOL_ADMIN_INITIAL,
            'student' => UserInvitation::PURPOSE_STUDENT_INITIAL,
            'parent' => UserInvitation::PURPOSE_GUARDIAN_INITIAL,
            default => UserInvitation::PURPOSE_STAFF_INITIAL,
        };
    }
}
