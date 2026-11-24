<?php

declare(strict_types=1);

namespace App\Domains\School\Services;

use App\Domains\School\DTOs\InitialSchoolAdminProvisioningResult;
use App\Domains\School\Exceptions\InitialSchoolAdminProvisioningException;
use App\Domains\School\Models\School;
use App\Domains\System\Enums\AuditAction;
use App\Domains\System\Services\AuditService;
use App\Domains\User\DTOs\AccountProvisioningCommand;
use App\Domains\User\DTOs\AccountProvisioningContext;
use App\Domains\User\Services\AccountProvisioningService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Throwable;

/**
 * Orchestrates the initial School Admin creation from the Super Admin panel.
 *
 * AccountProvisioningService owns the account/invitation lifecycle. This layer
 * adds only the responsibilities the legacy setup flow had: school and actor
 * guards, idempotence, the exact school-level permission grant and one audit
 * event. Everything runs in one outer transaction so a user is never left
 * without permissions or with a partial invitation.
 */
final class InitialSchoolAdminProvisioningService
{
    public function __construct(
        private readonly AccountProvisioningService $accountProvisioningService,
        private readonly SchoolAdminProvisioningService $schoolAdminProvisioningService,
        private readonly SchoolAdminPermissionGrantService $permissionGrantService,
        private readonly AuditService $auditService,
    ) {
    }

    public function provision(
        School $school,
        string $firstName,
        string $lastName,
        string $email,
        ?User $actor,
    ): InitialSchoolAdminProvisioningResult {
        if (! $actor instanceof User
            || $actor->trashed()
            || ! $actor->isActive()
            || ! $actor->hasRole('super_admin')) {
            throw InitialSchoolAdminProvisioningException::invalidActor();
        }

        $platformActor = $actor;
        $firstName = trim($firstName);
        $lastName = trim($lastName);
        $email = Str::lower(trim($email));

        if ($firstName === '' || $lastName === '' || $email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw InitialSchoolAdminProvisioningException::invalidInput();
        }

        try {
            return DB::transaction(function () use ($school, $firstName, $lastName, $email, $platformActor): InitialSchoolAdminProvisioningResult {
                $freshSchool = School::query()
                    ->whereKey($school->getKey())
                    ->lockForUpdate()
                    ->first();

                if (! $freshSchool instanceof School || $freshSchool->trashed() || ! $freshSchool->isActive()) {
                    throw InitialSchoolAdminProvisioningException::schoolNotAvailable();
                }

                $domain = $this->canonicalDomain($freshSchool);
                if ($domain === '') {
                    throw InitialSchoolAdminProvisioningException::schoolNotAvailable();
                }

                if ($this->schoolAdminProvisioningService->findSchoolAdmin($freshSchool) instanceof User) {
                    throw InitialSchoolAdminProvisioningException::schoolAdminAlreadyExists();
                }

                $role = Role::findOrCreate(SchoolAdminProvisioningService::ROLE, 'web');

                $provisioning = $this->accountProvisioningService->provision(
                    new AccountProvisioningCommand(
                        schoolId: (string) $freshSchool->getKey(),
                        firstName: $firstName,
                        lastName: $lastName,
                        email: $email,
                        roleName: SchoolAdminProvisioningService::ROLE,
                    ),
                    AccountProvisioningContext::forPlatform($platformActor),
                );

                $this->permissionGrantService->grant($role);

                $this->auditService->record([
                    'school_id' => $freshSchool->getKey(),
                    'auditable_type' => User::class,
                    'auditable_id' => (string) $provisioning->user->getKey(),
                    'action' => AuditAction::Provisioned->value,
                    'event' => 'school_admin.provisioned',
                    'description' => 'Compte initial d’administrateur d’établissement créé avec un lien d’activation à usage unique.',
                    'new_values' => [
                        'email' => $provisioning->user->email,
                        'role' => SchoolAdminProvisioningService::ROLE,
                        'status' => $provisioning->user->status->value,
                    ],
                    'user_id' => (string) $platformActor->getKey(),
                    'user_type' => User::class,
                ]);

                return new InitialSchoolAdminProvisioningResult(
                    admin: $provisioning->user,
                    created: true,
                    provisioning: $provisioning,
                    activationUrl: $this->buildActivationUrl($domain, $provisioning->invitationIssue->plainToken),
                );
            });
        } catch (InitialSchoolAdminProvisioningException $exception) {
            throw $exception;
        } catch (Throwable) {
            // Never surface an internal message, SQL detail or token to the UI.
            if (User::withTrashed()->where('email', $email)->exists()) {
                throw InitialSchoolAdminProvisioningException::duplicateEmail();
            }

            throw InitialSchoolAdminProvisioningException::provisioningFailed();
        }
    }

    private function canonicalDomain(School $school): string
    {
        return trim((string) ($school->domains()->value('domain') ?? $school->domain));
    }

    private function buildActivationUrl(string $domain, string $plainToken): string
    {
        $path = route('school.account.activation.show', ['token' => $plainToken], false);
        $scheme = request()->getScheme() ?: 'https';

        return $scheme.'://'.$domain.$path;
    }
}
