<?php

declare(strict_types=1);

namespace App\Filament\Resources\CentralUserResource\Pages;

use App\Domains\Role\Exceptions\RoleChangeException;
use App\Filament\Resources\CentralUserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateCentralUser extends CreateRecord
{
    protected static string $resource = CentralUserResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $roles = $this->form->getRawState()['roles'] ?? [];

        return CentralUserResource::stripPasswordForSchoolAdmin($data, is_array($roles) ? $roles : [], null);
    }

    protected function afterCreate(): void
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
