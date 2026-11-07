<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuperAdminSchoolResource\Pages;

use App\Domains\School\Models\School;
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

class EditSuperAdminSchool extends EditRecord
{
    protected static string $resource = SuperAdminSchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('provision_school_admin')
                ->label('Créer l\'administrateur')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->visible(fn (): bool => $this->schoolAdmin() === null)
                ->schema([
                    TextInput::make('name')
                        ->label('Nom complet')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $school = $this->school();

                    if (! $school instanceof School) {
                        return;
                    }

                    $result = app(SchoolAdminProvisioningService::class)->provision(
                        $school,
                        (string) $data['name'],
                        (string) $data['email'],
                        $this->actor(),
                    );

                    $this->notifySetupLink($result->setupUrl, $result->created);
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
