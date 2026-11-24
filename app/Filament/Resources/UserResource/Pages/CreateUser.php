<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Domains\School\Models\School;
use App\Domains\User\DTOs\AccountProvisioningCommand;
use App\Domains\User\DTOs\AccountProvisioningContext;
use App\Domains\User\DTOs\AccountProvisioningResult;
use App\Domains\User\Exceptions\AccountProvisioningException;
use App\Domains\User\Services\AccountProvisioningService;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string | Htmlable
    {
        if ($this->isSchoolAdminProvisioning()) {
            return 'Créer un compte du personnel';
        }

        return parent::getTitle();
    }

    public function getHeading(): string | Htmlable | null
    {
        if ($this->isSchoolAdminProvisioning()) {
            return 'Créer un compte du personnel';
        }

        return parent::getHeading();
    }

    public function form(Schema $schema): Schema
    {
        if ($this->isSchoolAdminProvisioning()) {
            return UserResource::schoolAdminProvisioningForm($schema);
        }

        return parent::form($schema);
    }

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if (! $this->isSchoolAdminProvisioning()) {
            return;
        }

        $actor = auth()->user();
        $tenantId = (string) (tenant()?->getKey() ?? '');

        if (! $actor instanceof User
            || $actor->trashed()
            || ! $actor->isActive()
            || ! $actor->hasRole('school_admin')
            || $tenantId === ''
            || (string) $actor->school_id !== $tenantId) {
            abort(403);
        }
    }

    public function create(bool $another = false): void
    {
        if (! $this->isSchoolAdminProvisioning()) {
            parent::create($another);

            return;
        }

        if ($this->isCreating) {
            return;
        }

        $this->isCreating = true;
        $this->authorizeAccess();

        try {
            $data = $this->form->getState();
            $result = $this->provisionStaff($data);
        } catch (ValidationException $exception) {
            $this->isCreating = false;

            throw $exception;
        } catch (AccountProvisioningException $exception) {
            $this->isCreating = false;

            if ($exception->reason === AccountProvisioningException::REASON_EMAIL) {
                throw ValidationException::withMessages([
                    'data.email' => 'Cette adresse e-mail est déjà associée à un compte, y compris un compte supprimé.',
                ]);
            }

            if ($exception->reason === AccountProvisioningException::REASON_ROLE) {
                throw ValidationException::withMessages([
                    'data.role' => 'Ce rôle ne peut pas être attribué lors de la création d’un compte.',
                ]);
            }

            $this->notifyFailure($this->failureMessageFor($exception));

            return;
        } catch (InvalidArgumentException) {
            $this->isCreating = false;
            $this->notifyFailure('Seuls les administrateurs d’établissement actifs peuvent créer un compte.');

            return;
        } catch (Throwable) {
            $this->isCreating = false;
            $this->notifyFailure('Le compte n’a pas pu être créé. Aucune modification n’a été conservée.');

            return;
        }

        $this->isCreating = false;
        $this->record = $result->user;
        $this->notifyActivationLink($result);
        $this->redirect($this->getRedirectUrl());
    }

    public function canCreateAnother(): bool
    {
        if ($this->isSchoolAdminProvisioning()) {
            return false;
        }

        return parent::canCreateAnother();
    }

    protected function getCreateFormAction(): Action
    {
        $action = parent::getCreateFormAction();

        return $this->isSchoolAdminProvisioning()
            ? $action->label('Créer le compte')
            : $action;
    }

    protected function getCancelFormAction(): Action
    {
        $action = parent::getCancelFormAction();

        return $this->isSchoolAdminProvisioning()
            ? $action->label('Annuler')
            : $action;
    }

    protected function getRedirectUrl(): string
    {
        if ($this->isSchoolAdminProvisioning()) {
            return $this->getResourceUrl();
        }

        return parent::getRedirectUrl();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $panelId = filament()->getCurrentPanel()->getId();

        if ($panelId === 'school-admin') {
            $data['school_id'] = tenant()?->id;
        }

        return $data;
    }

    private function isSchoolAdminProvisioning(): bool
    {
        return filament()->getCurrentPanel()->getId() === 'school-admin';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function provisionStaff(array $data): AccountProvisioningResult
    {
        $tenant = tenant();

        if (! $tenant instanceof School) {
            throw new AccountProvisioningException(
                'Tenant context is missing.',
                AccountProvisioningException::REASON_SCHOOL,
            );
        }

        $domain = trim((string) ($tenant->domains()->value('domain') ?? $tenant->domain));
        if ($domain === '') {
            throw new AccountProvisioningException(
                'Canonical tenant domain is missing.',
                AccountProvisioningException::REASON_SCHOOL,
            );
        }

        $actor = auth()->user();
        if (! $actor instanceof User) {
            throw new AccountProvisioningException(
                'Provisioning actor is missing.',
                AccountProvisioningException::REASON_ACTOR,
            );
        }

        $phone = trim((string) ($data['phone'] ?? ''));

        return app(AccountProvisioningService::class)->provision(
            new AccountProvisioningCommand(
                schoolId: (string) $tenant->getKey(),
                firstName: (string) ($data['first_name'] ?? ''),
                lastName: (string) ($data['last_name'] ?? ''),
                email: (string) ($data['email'] ?? ''),
                roleName: (string) ($data['role'] ?? ''),
                phone: $phone === '' ? null : $phone,
            ),
            AccountProvisioningContext::forSchoolAdmin($actor),
        );
    }

    private function failureMessageFor(AccountProvisioningException $exception): string
    {
        return match ($exception->reason) {
            AccountProvisioningException::REASON_ACTOR => 'Seuls les administrateurs d’établissement actifs peuvent créer un compte.',
            AccountProvisioningException::REASON_SCHOOL => 'L’établissement courant n’est pas disponible pour cette opération.',
            default => 'Le compte n’a pas pu être créé. Aucune modification n’a été conservée.',
        };
    }

    private function notifyActivationLink(AccountProvisioningResult $result): void
    {
        $tenant = tenant();
        $domain = $tenant instanceof School
            ? trim((string) ($tenant->domains()->value('domain') ?? $tenant->domain))
            : '';
        $path = route('school.account.activation.show', ['token' => $result->invitationIssue->plainToken], false);
        $scheme = request()->getScheme() ?: 'https';
        $url = $scheme.'://'.$domain.$path;

        Notification::make()
            ->title('Lien d’activation')
            ->body(
                'Lien personnel et à usage unique, valable 48 heures : '.$url.
                '. Il permet au destinataire de définir son propre mot de passe. Le compte ne sera accessible qu’après activation.'.
                ' Transmettez ce lien de manière sécurisée. Aucun e-mail ni SMS n’a été envoyé.'
            )
            ->success()
            ->send();
    }

    private function notifyFailure(string $message): void
    {
        Notification::make()
            ->title('Création impossible')
            ->body($message)
            ->danger()
            ->send();
    }
}
