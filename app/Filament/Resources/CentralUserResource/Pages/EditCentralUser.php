<?php

declare(strict_types=1);

namespace App\Filament\Resources\CentralUserResource\Pages;

use App\Domains\Role\Exceptions\RoleChangeException;
use App\Filament\Resources\CentralUserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditCentralUser extends EditRecord
{
    protected static string $resource = CentralUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();
        $roles = $this->form->getRawState()['roles'] ?? [];

        return CentralUserResource::stripPasswordForSchoolAdmin(
            $data,
            is_array($roles) ? $roles : [],
            $record instanceof User ? $record : null,
        );
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        if (! $record instanceof User) {
            throw new RoleChangeException('L’utilisateur cible n’est pas disponible.');
        }

        $roleIds = $this->form->getRawState()['roles'] ?? [];

        try {
            CentralUserResource::syncRolesFromFormState($record, is_array($roleIds) ? $roleIds : []);
        } catch (RoleChangeException) {
            throw ValidationException::withMessages([
                'roles' => 'Les rôles n’ont pas pu être enregistrés. Aucune modification n’a été conservée.',
            ]);
        }
    }
}
