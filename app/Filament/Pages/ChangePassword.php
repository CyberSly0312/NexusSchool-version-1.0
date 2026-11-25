<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Domains\User\DTOs\PasswordChangeCommand;
use App\Domains\User\Enums\PasswordChangeFailure;
use App\Domains\User\Exceptions\PasswordChangeException;
use App\Domains\User\Policies\PasswordPolicy;
use App\Domains\User\Services\UserPasswordChangeService;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;
use LogicException;

/**
 * Available in every panel. The form always targets the authenticated user:
 * no account selector or user identifier is exposed.
 */
final class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Modifier mon mot de passe';

    protected static ?string $title = 'Modifier mon mot de passe';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.change-password';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->passwordForm()->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('current_password')
                    ->label('Mot de passe actuel')
                    ->password()
                    ->revealable()
                    ->autocomplete('current-password')
                    ->required()
                    ->dehydrated(true)
                    ->validationMessages([
                        'required' => 'Le mot de passe actuel est obligatoire.',
                    ]),
                TextInput::make('new_password')
                    ->label('Nouveau mot de passe')
                    ->password()
                    ->revealable()
                    ->autocomplete('new-password')
                    ->required()
                    ->minLength(PasswordPolicy::PERSONAL_MIN_LENGTH)
                    ->same('new_password_confirmation')
                    ->dehydrated(true)
                    ->validationMessages(PasswordPolicy::fieldValidationMessages()),
                TextInput::make('new_password_confirmation')
                    ->label('Confirmer le nouveau mot de passe')
                    ->password()
                    ->revealable()
                    ->autocomplete('new-password')
                    ->required()
                    ->dehydrated(true)
                    ->validationMessages([
                        'required' => 'La confirmation du nouveau mot de passe est obligatoire.',
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $actor = auth()->user();

        if (! $actor instanceof User) {
            Notification::make()
                ->title('Modification impossible')
                ->body('Aucun utilisateur connecté n’a été reconnu.')
                ->danger()
                ->send();

            return;
        }

        $data = $this->passwordForm()->getState();

        try {
            app(UserPasswordChangeService::class)->change(new PasswordChangeCommand(
                actor: $actor,
                currentPassword: (string) ($data['current_password'] ?? ''),
                newPassword: (string) ($data['new_password'] ?? ''),
                newPasswordConfirmation: (string) ($data['new_password_confirmation'] ?? ''),
                currentSessionId: session()->getId(),
            ));
        } catch (PasswordChangeException $exception) {
            if ($exception->failure === PasswordChangeFailure::CurrentPassword) {
                throw ValidationException::withMessages([
                    'data.current_password' => [$exception->getMessage()],
                ]);
            }

            if ($exception->failure === PasswordChangeFailure::Validation) {
                throw ValidationException::withMessages([
                    'data.new_password' => $exception->validationMessages !== []
                        ? $exception->validationMessages
                        : [$exception->getMessage()],
                ]);
            }

            Notification::make()
                ->title('Modification impossible')
                ->body($exception->getMessage())
                ->danger()
                ->send();

            return;
        }

        $this->passwordForm()->fill();

        Notification::make()
            ->title('Mot de passe modifié')
            ->body('Votre nouveau mot de passe a été enregistré. Les autres sessions et les liens d’accès ont été révoqués.')
            ->success()
            ->send();
    }

    private function passwordForm(): Schema
    {
        $schema = $this->getSchema('form');

        if (! $schema instanceof Schema) {
            throw new LogicException('Le formulaire de modification du mot de passe n’est pas disponible.');
        }

        return $schema;
    }
}
