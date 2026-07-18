<?php

declare(strict_types=1);

namespace App\Filament\Resources\RoleResource\RelationManagers;

use App\Domains\Permission\Enums\PermissionGroup;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;

class PermissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';

    protected static ?string $title = 'Permissions';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('name')
                    ->label('Permission')
                    ->options(Permission::pluck('name', 'name'))
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
                    ->sortable()
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, '.view') => 'info',
                        str_contains($state, '.create') => 'success',
                        str_contains($state, '.update') => 'warning',
                        str_contains($state, '.delete') => 'danger',
                        default => 'gray',
                    }),
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
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Group')
                    ->options(PermissionGroup::options())
                    ->query(function ($query, array $data): void {
                        if ($data['value']) {
                            $query->where('name', 'like', "{$data['value']}.%");
                        }
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Assign Permission')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['guard_name'] = $data['guard_name'] ?? 'web';

                        return $data;
                    }),
                Tables\Actions\AssociateAction::make()
                    ->label('Link Existing Permission')
                    ->recordSelect(
                        Forms\Components\Select::make('permission_id')
                            ->options(Permission::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                    )
                    ->associateRelatedRecordUsing(function ($record, $data) {
                        $permission = Permission::findOrFail($data['permission_id']);
                        $record->givePermissionTo($permission);
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
