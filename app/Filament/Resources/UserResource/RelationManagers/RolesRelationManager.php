<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Role;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $title = 'Roles';

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

    public static function canView(): bool
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
                    ->label('Role')
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
                Tables\Actions\CreateAction::make()
                    ->label('Assign Role')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['guard_name'] = $data['guard_name'] ?? 'web';

                        return $data;
                    })
                    ->visible(! self::isSchoolAdminPanel()),
                Tables\Actions\AssociateAction::make()
                    ->label('Link Existing Role')
                    ->recordSelect(
                        Forms\Components\Select::make('role_id')
                            ->options(fn () => self::isSchoolAdminPanel()
                                ? Role::whereIn('name', self::$schoolAdminAllowedRoles)->pluck('name', 'id')
                                : Role::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                    )
                    ->associateRelatedRecordUsing(function ($record, $data) {
                        $role = Role::findOrFail($data['role_id']);
                        $record->roles()->attach($role);
                    }),
            ])
            ->actions([
                Tables\Actions\DissociateAction::make()
                    ->label('Remove'),
            ])
            ->bulkActions([
                Tables\Actions\DissociateBulkAction::make(),
            ]);
    }
}
