<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Campus\Enums\CampusStatus;
use App\Filament\Resources\CampusResource\Pages;
use App\Models\Campus;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use App\Traits\ScopedByTenant;

class CampusResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Campus::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static string|\UnitEnum|null $navigationGroup = 'Établissement';

    protected static ?string $navigationLabel = 'Campus / Annexes';

    protected static ?string $modelLabel = 'Campus';

    protected static ?string $pluralModelLabel = 'Campus';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Section::make('Informations du campus')
                ->icon('heroicon-o-building-office-2')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom du campus')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('code')
                        ->label('Code')
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true),
                    Forms\Components\Toggle::make('is_main')
                        ->label('Campus principal')
                        ->default(false),
                    Forms\Components\TextInput::make('display_order')
                        ->label('Ordre d\'affichage')
                        ->numeric()
                        ->default(0),
                ])->columns(2),

            Forms\Components\Section::make('Coordonnées')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Forms\Components\TextInput::make('address')
                        ->label('Adresse')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('city')
                        ->label('Ville')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('Téléphone')
                        ->tel()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255),
                ])->columns(2),

            Forms\Components\Section::make('Description')
                ->schema([
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3),
                ]),

            Forms\Components\Section::make('Statut')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options(CampusStatus::class)
                        ->default(CampusStatus::Active)
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_main')
                    ->boolean()
                    ->label('Principal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable()
                    ->hidden(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (CampusStatus $state): string => $state->label())
                    ->color(fn (CampusStatus $state): string|array|null => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('display_order')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(CampusStatus::class),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampuses::route('/'),
            'create' => Pages\CreateCampus::route('/create'),
            'edit' => Pages\EditCampus::route('/{record}/edit'),
        ];
    }
}
