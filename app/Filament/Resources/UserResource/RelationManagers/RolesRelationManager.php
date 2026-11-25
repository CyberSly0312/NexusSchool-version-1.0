<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Domains\Role\DTOs\RoleChangeCommand;
use App\Domains\Role\DTOs\RoleChangeContext;
use App\Domains\Role\Enums\RoleChangeOperation;
use App\Domains\Role\Exceptions\RoleChangeException;
use App\Domains\Role\Policies\SchoolAdminRolePolicy;
use App\Domains\Role\Services\UserRoleChangeService;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Throwable;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $title = 'Rôles';

    public function mount(): void
    {
        parent::mount();

        try {
            $context = $this->roleChangeContext();
            $owner = $this->getOwnerRecord();

            if (! $owner instanceof User) {
                throw new RoleChangeException('L’utilisateur cible n’est pas disponible.');
            }

            app(UserRoleChangeService::class)->assertTargetIsAccessible($owner, $context);
        } catch (Throwable) {
            abort(403, 'Accès refusé.');
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Rôle')
                    ->formatStateUsing(fn (string $state): string => SchoolAdminRolePolicy::label($state))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Portail')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Attribuer un rôle existant')
                    ->modalHeading('Attribuer un rôle')
                    ->modalSubmitActionLabel('Attribuer')
                    ->modalCancelActionLabel('Annuler')
                    ->successNotificationTitle('Rôle attribué.')
                    ->failureNotificationTitle('Attribution impossible.')
                    ->failureNotificationBody('La modification demandée n’a pas été appliquée.')
                    ->authorize(fn (): bool => $this->canManageOwner())
                    ->recordSelect(fn (Select $select): Select => $select
                        ->label('Rôle'))
                    ->recordSelectOptionsQuery(fn (Builder $query): Builder => $this->scopeRoleOptions($query))
                    ->attachAnother(false)
                    ->before(function (AttachAction $action, array $data): void {
                        $roleIds = $data['recordId'] ?? null;

                        try {
                            app(UserRoleChangeService::class)->validate(
                                $this->makeCommand($roleIds, RoleChangeOperation::Assign),
                                $this->roleChangeContext(),
                            );
                        } catch (Throwable) {
                            $this->failAction($action);
                        }
                    })
                    ->using(function (Model $record, AttachAction $action): void {
                        try {
                            app(UserRoleChangeService::class)->apply(
                                $this->makeCommand($record->getKey(), RoleChangeOperation::Assign),
                                $this->roleChangeContext(),
                            );
                        } catch (Throwable) {
                            $this->failAction($action);
                        }
                    }),
            ])
            ->actions([
                DetachAction::make()
                    ->label('Retirer')
                    ->modalHeading('Retirer le rôle')
                    ->modalDescription('Le rôle sélectionné sera retiré à cet utilisateur.')
                    ->modalSubmitActionLabel('Confirmer le retrait')
                    ->modalCancelActionLabel('Annuler')
                    ->successNotificationTitle('Rôle retiré.')
                    ->failureNotificationTitle('Retrait impossible.')
                    ->failureNotificationBody('La modification demandée n’a pas été appliquée.')
                    ->authorize(fn (): bool => $this->canManageOwner())
                    ->using(function (Model $record, DetachAction $action): void {
                        try {
                            app(UserRoleChangeService::class)->apply(
                                $this->makeCommand($record->getKey(), RoleChangeOperation::Remove),
                                $this->roleChangeContext(),
                            );
                        } catch (Throwable) {
                            $this->failAction($action);
                        }
                    }),
            ])
            ->bulkActions([
                DetachBulkAction::make()
                    ->label('Retirer la sélection')
                    ->modalHeading('Retirer les rôles sélectionnés')
                    ->modalDescription('Les rôles sélectionnés seront retirés à cet utilisateur.')
                    ->modalSubmitActionLabel('Confirmer le retrait')
                    ->modalCancelActionLabel('Annuler')
                    ->successNotificationTitle('Rôles retirés.')
                    ->failureNotificationTitle('Retrait impossible.')
                    ->failureNotificationBody('La modification demandée n’a pas été appliquée.')
                    ->authorize(fn (): bool => $this->canManageOwner())
                    ->using(function (EloquentCollection | Collection $records, DetachBulkAction $action): void {
                        $roleIds = $records
                            ->map(fn (mixed $role): int|string => $role instanceof Model ? $role->getKey() : $role)
                            ->all();

                        try {
                            app(UserRoleChangeService::class)->apply(
                                $this->makeCommand($roleIds, RoleChangeOperation::Remove),
                                $this->roleChangeContext(),
                            );
                        } catch (Throwable) {
                            $this->failAction($action);
                        }
                    }),
            ]);
    }

    private function canManageOwner(): bool
    {
        try {
            $owner = $this->getOwnerRecord();

            if (! $owner instanceof User) {
                return false;
            }

            app(UserRoleChangeService::class)->assertTargetIsAccessible(
                $owner,
                $this->roleChangeContext(),
            );

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function roleChangeContext(): RoleChangeContext
    {
        $actor = auth()->user();

        if (! $actor instanceof User) {
            throw new RoleChangeException('Aucun administrateur actif n’est connecté.');
        }

        return RoleChangeContext::forActor($actor, $this->tenantSchoolId());
    }

    private function tenantSchoolId(): ?string
    {
        if (filament()->getCurrentPanel()?->getId() !== 'school-admin') {
            return null;
        }

        return (string) (tenant()?->getKey() ?? '');
    }

    private function scopeRoleOptions(Builder $query): Builder
    {
        $query->where('guard_name', 'web');

        if ($this->roleChangeContext()->isSchoolAdminOperation()) {
            $query->whereIn('name', SchoolAdminRolePolicy::ALLOWED_ROLES);
        }

        return $query;
    }

    /**
     * @param  int|string|array<mixed>|null  $roleIds
     */
    private function makeCommand(
        int|string|array|null $roleIds,
        RoleChangeOperation $operation,
    ): RoleChangeCommand {
        $owner = $this->getOwnerRecord();

        if (! $owner instanceof User) {
            throw new RoleChangeException('L’utilisateur cible n’est pas disponible.');
        }

        $normalized = is_array($roleIds) ? $roleIds : [$roleIds];
        $context = $this->roleChangeContext();

        return new RoleChangeCommand(
            targetUserId: (string) $owner->getKey(),
            roleIds: array_values(array_filter(
                $normalized,
                static fn (mixed $roleId): bool => is_int($roleId) || is_string($roleId) || is_numeric($roleId),
            )),
            schoolId: $context->isSchoolAdminOperation() ? (string) ($context->tenantSchoolId ?? '') : null,
            operation: $operation,
        );
    }

    private function failAction(Action $action): void
    {
        $action->failure();
        $action->halt();
    }
}
