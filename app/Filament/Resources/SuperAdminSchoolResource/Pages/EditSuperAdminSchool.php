<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuperAdminSchoolResource\Pages;

use App\Domains\AccountRecovery\DTOs\SchoolAdminResetCommand;
use App\Domains\AccountRecovery\Exceptions\PasswordResetException;
use App\Domains\AccountRecovery\Services\SchoolAdminPasswordResetService;
use App\Domains\School\DTOs\InitialSchoolAdminProvisioningResult;
use App\Domains\School\DTOs\SchoolAdminLifecycleCommand;
use App\Domains\School\DTOs\SchoolAdminReplacementCommand;
use App\Domains\School\Enums\SchoolAdminLifecycleFailure;
use App\Domains\School\Exceptions\InitialSchoolAdminProvisioningException;
use App\Domains\School\Exceptions\SchoolAdminLifecycleException;
use App\Domains\School\Models\School;
use App\Domains\School\Services\InitialSchoolAdminProvisioningService;
use App\Domains\School\Services\SchoolAdminLifecycleService;
use App\Domains\School\Services\SchoolAdminProvisioningService;
use App\Filament\Resources\SuperAdminSchoolResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditSuperAdminSchool extends EditRecord
{
    protected static string $resource = SuperAdminSchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('provision_school_admin')
                ->label('Créer l’administrateur de l’établissement')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->modalHeading('Créer l’administrateur de l’établissement')
                ->modalDescription('Le compte sera créé en attente d’activation. Aucun mot de passe ne sera demandé ni envoyé. Un lien d’activation à usage unique sera affiché une seule fois à l’opérateur autorisé.')
                ->modalSubmitActionLabel('Créer le compte')
                ->visible(fn (): bool => $this->canProvisionSchoolAdmin())
                ->schema([
                    TextInput::make('first_name')
                        ->label('Prénom')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('last_name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->label('Adresse e-mail')
                        ->email()
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $school = $this->school();

                    if (! $school instanceof School) {
                        $this->notifyProvisioningFailure('Cet établissement n’est pas disponible pour cette opération.');

                        return;
                    }

                    $firstName = trim((string) ($data['first_name'] ?? ''));
                    $lastName = trim((string) ($data['last_name'] ?? ''));
                    $email = trim((string) ($data['email'] ?? ''));

                    try {
                        $result = app(InitialSchoolAdminProvisioningService::class)->provision(
                            $school,
                            $firstName,
                            $lastName,
                            $email,
                            $this->actor(),
                        );
                    } catch (InitialSchoolAdminProvisioningException $exception) {
                        if ($exception->reason === InitialSchoolAdminProvisioningException::REASON_DUPLICATE_EMAIL) {
                            throw ValidationException::withMessages([
                                'email' => $exception->getMessage(),
                            ]);
                        }

                        $this->notifyProvisioningFailure($exception->getMessage());

                        return;
                    } catch (\Throwable) {
                        $this->notifyProvisioningFailure('Le compte n’a pas pu être créé. Aucune modification n’a été conservée.');

                        return;
                    }

                    $this->notifyActivationLink($result);
                }),

            Action::make('issue_school_admin_setup_link')
                ->label('Générer un lien de configuration')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Générer un lien de configuration')
                ->modalDescription('Aucun mot de passe existant ne sera affiché ni modifié. Tout lien précédent non utilisé sera révoqué.')
                ->visible(fn (): bool => $this->recoverableSchoolAdmin() !== null)
                ->action(function (): void {
                    $school = $this->school();

                    if (! $school instanceof School) {
                        return;
                    }

                    $result = app(SchoolAdminProvisioningService::class)->issueSetupLink(
                        $school,
                        $this->actor(),
                    );

                    $this->notifySetupLink($result->setupUrl, false);
                }),

            Action::make('initiate_school_admin_reset')
                ->label('Réinitialiser le mot de passe')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Réinitialiser le mot de passe de l’administrateur')
                ->modalDescription('Le Super Admin ne choisit aucun mot de passe. Un lien à usage unique valable 24 heures sera affiché une seule fois ; l’administrateur définira lui-même son nouveau mot de passe. Aucun e-mail ni SMS n’a été envoyé.')
                ->modalSubmitActionLabel('Générer le lien de réinitialisation')
                ->visible(fn (): bool => $this->isSuperAdminActor() && $this->recoverableSchoolAdmin() !== null)
                ->action(function (): void {
                    $school = $this->school();
                    $actor = $this->actor();
                    $target = $this->recoverableSchoolAdmin();

                    if (! $school instanceof School || ! $actor instanceof User || ! $target instanceof User) {
                        $this->notifyLifecycleFailure('La réinitialisation n’a pas pu être demandée.');

                        return;
                    }

                    try {
                        $result = app(SchoolAdminPasswordResetService::class)->initiate(
                            new SchoolAdminResetCommand($actor, $school, (string) $target->getKey()),
                        );
                    } catch (PasswordResetException $exception) {
                        $this->notifyLifecycleFailure($exception->getMessage());

                        return;
                    } catch (\Throwable) {
                        $this->notifyLifecycleFailure('La réinitialisation n’a pas pu être demandée. Aucune modification n’a été conservée.');

                        return;
                    }

                    Notification::make()
                        ->title('Lien de réinitialisation')
                        ->body(
                            'Lien à usage unique valable 24 heures : '.$result->resetUrl.
                            '. Transmettez-le de manière sécurisée. Aucun e-mail ni SMS n’a été envoyé.'.
                            ' Tout lien précédent non utilisé a été révoqué.'
                        )
                        ->success()
                        ->persistent()
                        ->send();
                }),

            Action::make('suspend_school_admin')
                ->label('Suspendre l’administrateur')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Suspendre l’administrateur d’établissement')
                ->modalDescription('Le compte sera conservé avec son historique, mais l’authentification et tous les accès actifs seront révoqués. Le dernier administrateur actif ne peut pas être suspendu sans remplacement.')
                ->modalSubmitActionLabel('Suspendre le compte')
                ->visible(fn (): bool => $this->isSuperAdminActor() && $this->schoolAdmin() !== null)
                ->action(function (): void {
                    $school = $this->school();
                    $actor = $this->actor();
                    $target = $this->schoolAdmin();

                    if (! $school instanceof School || ! $actor instanceof User || ! $target instanceof User) {
                        $this->notifyLifecycleFailure('La suspension n’a pas pu être effectuée.');

                        return;
                    }

                    try {
                        app(SchoolAdminLifecycleService::class)->suspend(
                            new SchoolAdminLifecycleCommand($actor, $school, (string) $target->getKey()),
                        );
                    } catch (SchoolAdminLifecycleException $exception) {
                        $this->notifyLifecycleFailure($exception->getMessage(), $exception->failure === SchoolAdminLifecycleFailure::LastActive);

                        return;
                    } catch (\Throwable) {
                        $this->notifyLifecycleFailure('La suspension n’a pas pu être effectuée. Aucune modification n’a été conservée.');

                        return;
                    }

                    Notification::make()
                        ->title('Administrateur suspendu')
                        ->body('Le compte a été conservé. Son historique et ses références restent intactes ; toutes les sessions et tous les jetons actifs ont été révoqués.')
                        ->success()
                        ->send();
                }),

            Action::make('replace_school_admin')
                ->label('Remplacer l’administrateur')
                ->icon('heroicon-o-user-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Remplacer l’administrateur d’établissement')
                ->modalDescription('Un nouveau compte sera créé avec une adresse e-mail différente. L’ancien compte sera suspendu et conservé. Aucun mot de passe ne sera défini par le Super Admin. Aucun e-mail ni SMS n’a été envoyé.')
                ->modalSubmitActionLabel('Créer le remplaçant')
                ->visible(fn (): bool => $this->isSuperAdminActor() && $this->schoolAdmin() !== null)
                ->schema([
                    TextInput::make('first_name')
                        ->label('Prénom du remplaçant')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('last_name')
                        ->label('Nom du remplaçant')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('email')
                        ->label('Adresse e-mail du remplaçant')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    TextInput::make('phone')
                        ->label('Téléphone')
                        ->tel()
                        ->maxLength(50)
                        ->nullable(),
                ])
                ->action(function (array $data): void {
                    $school = $this->school();
                    $actor = $this->actor();
                    $target = $this->schoolAdmin();

                    if (! $school instanceof School || ! $actor instanceof User || ! $target instanceof User) {
                        $this->notifyLifecycleFailure('Le remplacement n’a pas pu être effectué.');

                        return;
                    }

                    try {
                        $result = app(SchoolAdminLifecycleService::class)->replace(
                            new SchoolAdminReplacementCommand(
                                actor: $actor,
                                school: $school,
                                targetUserId: (string) $target->getKey(),
                                firstName: (string) ($data['first_name'] ?? ''),
                                lastName: (string) ($data['last_name'] ?? ''),
                                email: (string) ($data['email'] ?? ''),
                                phone: isset($data['phone']) ? (string) $data['phone'] : null,
                            ),
                        );
                    } catch (SchoolAdminLifecycleException $exception) {
                        $this->notifyLifecycleFailure($exception->getMessage());

                        return;
                    } catch (\Throwable) {
                        $this->notifyLifecycleFailure('Le remplacement n’a pas pu être effectué. Aucune modification n’a été conservée.');

                        return;
                    }

                    Notification::make()
                        ->title('Remplacement effectué')
                        ->body(
                            'Lien d’activation personnel à usage unique valable 48 heures : '.$result->activationUrl.
                            '. Le remplaçant définira son propre mot de passe. L’ancien compte est suspendu et conservé.'.
                            ' Aucun e-mail ni SMS n’a été envoyé.'
                        )
                        ->success()
                        ->persistent()
                        ->send();
                }),

            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    private function school(): ?School
    {
        $record = $this->record;

        return $record instanceof School ? $record : null;
    }

    private function schoolAdmin(): ?User
    {
        $school = $this->school();

        if (! $school instanceof School) {
            return null;
        }

        return app(SchoolAdminProvisioningService::class)->findSchoolAdmin($school);
    }

    private function recoverableSchoolAdmin(): ?User
    {
        $school = $this->school();

        if (! $school instanceof School) {
            return null;
        }

        return app(SchoolAdminProvisioningService::class)->findRecoverableSchoolAdmin($school);
    }

    private function actor(): ?User
    {
        $actor = auth()->user();

        return $actor instanceof User ? $actor : null;
    }

    private function isSuperAdminActor(): bool
    {
        $actor = $this->actor();

        return $actor instanceof User
            && ! $actor->trashed()
            && $actor->isActive()
            && $actor->hasRole('super_admin');
    }

    private function canProvisionSchoolAdmin(): bool
    {
        $actor = $this->actor();
        $school = $this->school();

        if (! $actor instanceof User
            || $actor->trashed()
            || ! $actor->isActive()
            || ! $actor->hasRole('super_admin')) {
            return false;
        }

        if (! $school instanceof School || $school->trashed() || ! $school->isActive()) {
            return false;
        }

        return $this->schoolAdmin() === null;
    }

    private function notifyProvisioningFailure(string $message): void
    {
        Notification::make()
            ->title('Création impossible')
            ->body($message)
            ->danger()
            ->send();
    }

    private function notifyLifecycleFailure(string $message, bool $warning = false): void
    {
        $notification = Notification::make()
            ->title('Opération refusée')
            ->body($message);

        if ($warning) {
            $notification->warning();
        } else {
            $notification->danger();
        }

        $notification->send();
    }

    private function notifyActivationLink(InitialSchoolAdminProvisioningResult $result): void
    {
        if (! $result->created || $result->activationUrl === null) {
            Notification::make()
                ->title('Administrateur déjà présent')
                ->body('Un administrateur existe déjà pour cet établissement. Aucun doublon n’a été créé.')
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title('Lien d’activation')
            ->body(
                'Lien d’activation à usage unique valable 48 h : '.$result->activationUrl.
                '. Transmettre ce lien de manière sécurisée. Aucun e-mail ni SMS n’a été envoyé.'.
                ' Le compte restera inaccessible jusqu’à son activation. L’administrateur devra ensuite se connecter normalement.'
            )
            ->success()
            ->send();
    }

    private function notifySetupLink(?string $url, bool $created): void
    {
        if ($url === null) {
            Notification::make()
                ->title('Administrateur déjà présent')
                ->body('Un School Admin existe déjà pour cette école. Aucun doublon n\'a été créé.')
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title($created ? 'Administrateur créé' : 'Lien de configuration généré')
            ->body('Lien à usage unique valable 24 h : '.$url)
            ->success()
            ->persistent()
            ->send();
    }
}
