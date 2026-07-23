<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Permission\Enums\PermissionGroup;
use App\Domains\Permission\Services\PermissionService;
use App\Filament\Resources\PermissionResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Permissions';

    protected static ?string $modelLabel = 'Permission';

    protected static ?string $pluralModelLabel = 'Permissions';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 25;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Détails de la permission')
                    ->schema([
                        Forms\Components\Select::make('group')
                            ->label('Groupe')
                            ->options(PermissionGroup::options())
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state): void {
                                if ($state) {
                                    $group = PermissionGroup::from($state);
                                    $set('name', $group->value.'.');
                                }
                            })
                            ->helperText('Sélectionner un groupe de module'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->helperText('Format : module.action (ex. : school.view)')
                            ->suffix('web'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, '.view') => 'info',
                        str_contains($state, '.create') => 'success',
                        str_contains($state, '.update') => 'warning',
                        str_contains($state, '.delete') => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('group')
                    ->label('Groupe')
                    ->state(function (Permission $record): string {
                        $parts = explode('.', $record->name);

                        return $parts[0] ?? $record->name;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles_count')
                    ->counts('roles')
                    ->label('Rôles')
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Garde')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Groupe')
                    ->options(PermissionGroup::options())
                    ->query(function ($query, array $data): void {
                        if ($data['value']) {
                            $query->where('name', 'like', "{$data['value']}.%");
                        }
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Permission $record, DeleteAction $action) {
                        $service = new PermissionService();
                        if ($service->isSystemPermission($record)) {
                            $action->label('Permission système');
                            $action->disabled();
                        }
                    }),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
