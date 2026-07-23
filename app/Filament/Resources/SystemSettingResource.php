<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\System\Enums\SettingValueType;
use App\Filament\Resources\SystemSettingResource\Pages;
use App\Models\SystemSetting;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;

    protected static ?string $recordTitleAttribute = 'key';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration système';

    protected static ?string $navigationLabel = 'Paramètres système';

    protected static ?string $modelLabel = 'Paramètre système';

    protected static ?string $pluralModelLabel = 'Paramètres système';

    protected static ?int $navigationSort = 90;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails du paramètre')
                    ->schema([
                        Forms\Components\TextInput::make('group')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('key')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Select::make('type')
                            ->options(SettingValueType::labels())
                            ->default('string')
                            ->required(),
                    ])
                    ->columns(3),

                SectionComponent::make('Valeur')
                    ->schema([
                        Forms\Components\Textarea::make('value')
                            ->rows(3),
                        Forms\Components\Toggle::make('is_readonly')
                            ->default(false),
                        Forms\Components\Toggle::make('is_system')
                            ->default(false),
                    ])
                    ->columns(3),

                SectionComponent::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('value')
                    ->limit(50)
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                IconColumn::make('is_readonly')
                    ->boolean()
                    ->toggleable(),
                IconColumn::make('is_system')
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->options(fn () => SystemSetting::distinct()->pluck('group', 'group')->toArray()),
                SelectFilter::make('type')
                    ->options(SettingValueType::labels()),
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
            'index' => Pages\ListSystemSettings::route('/'),
            'create' => Pages\CreateSystemSetting::route('/create'),
            'edit' => Pages\EditSystemSetting::route('/{record}/edit'),
        ];
    }
}
