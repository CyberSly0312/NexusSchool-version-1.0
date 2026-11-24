<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Role;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $title = 'Rôles';

    protected static ?string $recordTitleAttribute = 'name';

    private static array $schoolAdminAllowedRoles = [
        'school_admin',
        'director',
        'teacher',
        'student',
        'parent',
        'accountant',
        'secretary',
    ];

    private static function isSchoolAdminPanel(): bool
    {
        return filament()->getCurrentPanel()->getId() === 'school-admin';
    }

    public function canView(Model $record): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        $query = Role::query();

        if (self::isSchoolAdminPanel()) {
            $query->whereIn('name', self::$schoolAdminAllowedRoles);
        }

        return $schema
            ->schema([
                Forms\Components\Select::make('name')
                    ->label('Rôle')
                    ->options(fn () => $query->pluck('name', 'name'))
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Attribuer un rôle')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['guard_name'] = $data['guard_name'] ?? 'web';

                        return $data;
                    })
                    ->visible(! self::isSchoolAdminPanel()),
                Actions\AssociateAction::make()
                    ->label('Lier un rôle existant')
                    ->recordSelect(function (Forms\Components\Select $select) {
                        return $select
                            ->options(fn () => self::isSchoolAdminPanel()
                                ? Role::whereIn('name', self::$schoolAdminAllowedRoles)->pluck('name', 'id')
                                : Role::pluck('name', 'id'))
                            ->searchable()
                            ->preload();
                    })
                    ->associateRelatedRecordUsing(function ($record, $data) {
                        $role = Role::findOrFail($data['role_id']);
                        $record->roles()->attach($role);
                    }),
            ])
            ->actions([
                Actions\DissociateAction::make()
                    ->label('Retirer'),
            ])
            ->bulkActions([
                Actions\DissociateBulkAction::make()
                    ->label('Retirer la sélection'),
            ]);
    }
}
