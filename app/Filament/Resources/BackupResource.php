<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\System\Enums\BackupStatus;
use App\Domains\System\Enums\BackupType;
use App\Filament\Resources\BackupResource\Pages;
use App\Models\Backup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BackupResource extends Resource
{
    protected static ?string $model = Backup::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration système';

    protected static ?string $navigationLabel = 'Sauvegardes';

    protected static ?string $modelLabel = 'Sauvegarde';

    protected static ?string $pluralModelLabel = 'Sauvegardes';

    protected static ?int $navigationSort = 92;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails de la sauvegarde')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Select::make('type')
                            ->options(BackupType::labels())
                            ->default('manual'),
                        Forms\Components\Select::make('disk')
                            ->options(['local' => 'Local', 's3' => 'S3'])
                            ->default('local'),
                        Forms\Components\Repeater::make('tables_included')
                            ->schema([
                                Forms\Components\TextInput::make('table')
                                    ->label('Table'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter une table'),
                        Forms\Components\Textarea::make('notes')
                            ->rows(2),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => BackupStatus::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('disk')
                    ->toggleable(),
                TextColumn::make('size_bytes')
                    ->label('Taille')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 1) . ' Ko' : '-')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('duration_ms')
                    ->label('Durée')
                    ->formatStateUsing(fn ($state) => $state ? round($state / 1000, 1) . 's' : '-')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('completed_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(BackupStatus::labels()),
                SelectFilter::make('type')
                    ->options(BackupType::labels()),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => Pages\ListBackups::route('/'),
            'create' => Pages\CreateBackup::route('/create'),
            'edit' => Pages\EditBackup::route('/{record}/edit'),
        ];
    }
}
