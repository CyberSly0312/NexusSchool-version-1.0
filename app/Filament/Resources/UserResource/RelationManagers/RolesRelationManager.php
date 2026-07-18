<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $title = 'Roles';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('name')
                    ->label('Role')
                    ->options(Role::pluck('name', 'name'))
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
                    }),
                Tables\Actions\AssociateAction::make()
                    ->label('Link Existing Role')
                    ->recordSelect(
                        Forms\Components\Select::make('role_id')
                            ->options(Role::pluck('name', 'id'))
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
